<?php

namespace App\Repositories;

use App\Models\Address;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\User;
use Auth;
use Exception;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Illuminate\Support\Carbon;

/**
 * Class PatientRepository
 *
 * @version February 14, 2020, 5:53 am UTC
 */
class PatientRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Patient::class;
    }

    /**
     * @param  array  $input
     * @param  bool  $mail
     * @return bool
     */
    public function store($input, $mail = false)
    {
        try {
            $input['phone'] = preparePhoneNumber($input, 'phone');
            $input['emergencyPhone'] = preparePhoneNumber($input, 'emergencyPhone');
            $input['department_id'] = Department::whereName('Patient')->first()->id;
            $input['password'] = Hash::make('infinitewellnesspk');
            $input['dob'] = (! empty($input['dob'])) ? $input['dob'] : null;
            $input['email'] =  null;
            $input['reffer_by'] = $input['reffer_by'] ?? null;
            $user = User::create($input);
            if ($mail) {
                $user->sendEmailVerificationNotification();
            }

            if (isset($input['image']) && ! empty($input['image'])) {
                $mediaId = storeProfileImage($user, $input['image']);
            }

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->format('Y');
            $mrNum = $formattedDate . '-'.$user->id;
            $patient = Patient::create(['user_id' => $user->id, 'MR' => $mrNum]);

            $ownerId = $patient->id;
            $ownerType = Patient::class;

            if (! empty($address = Address::prepareAddressArray($input))) {
                Address::create(array_merge($address, ['owner_id' => $ownerId, 'owner_type' => $ownerType]));
            }

            $user->update(['owner_id' => $ownerId, 'owner_type' => $ownerType]);
            $user->assignRole($input['department_id']);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
        return $patient->id;
        return true;
    }

    /**
     * @param  array  $input
     * @param  Patient  $patient
     * @return bool
     */
    public function update($input, $patient)
    {
        try {
            unset($input['password']);

            $user = User::find($patient->patientUser->id);
            if ($input['avatar_remove'] == 1 && isset($input['avatar_remove']) && ! empty($input['avatar_remove'])) {
                removeFile($user, User::COLLECTION_PROFILE_PICTURES);
            }
            if (isset($input['image']) && ! empty($input['image'])) {
                $mediaId = updateProfileImage($user, $input['image']);
            }

            /** @var Patient $patient */
            $input['phone'] = preparePhoneNumber($input, 'phone');
            $input['emergencyPhone'] = preparePhoneNumber($input, 'emergencyPhone');
            $input['dob'] = (! empty($input['dob'])) ? $input['dob'] : null;
            $patient->patientUser->update($input);
            $patient->update($input);

            if (! empty($patient->address)) {
                if (empty($address = Address::prepareAddressArray($input))) {
                    $patient->address->delete();
                }
                $patient->address->update($input);
            } else {
                if (! empty($address = Address::prepareAddressArray($input)) && empty($patient->address)) {
                    $ownerId = $patient->id;
                    $ownerType = Patient::class;
                    Address::create(array_merge($address, ['owner_id' => $ownerId, 'owner_type' => $ownerType]));
                }
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }

    public function getPatients(): \Illuminate\Support\Collection
    {
        //        $user = Auth::user();
        //        if ($user->hasRole('Doctor')) {
        //            $patients = getPatientsList($user->owner_id);
        //        } else {
        //            $patients = Patient::with('patientUser')
        //                ->whereHas('patientUser', function (Builder $query) {
        //                    $query->where('status', 1);
        //                })->get()->pluck('patientUser.full_name', 'id')->sort();
        //        }
        return Patient::with('patientUser')
            ->whereHas('patientUser', function (Builder $query) {
                $query->where('status', 1);
            })->get()->pluck('patientUser.full_name', 'id')->sort();
    }

    /**
     * @param  int  $patientId
     * @return mixed
     */
    public function getPatientAssociatedData($patientId)
    {
        $patientData = Patient::with([
            'bills', 'invoices', 'appointments.doctor.doctorUser', 'appointments.doctor.department', 'admissions.doctor.doctorUser',
            'cases.doctor.doctorUser', 'advancedpayments', 'documents.media', 'documents.documentType', 'patientUser',
            'vaccinations.vaccination',
            'address',
        ])->find($patientId);

        return $patientData;
    }

    /**
     * @param  array  $input
     */
    public function createNotification($input)
    {
        try {
            $receptionists = Receptionist::pluck('user_id', 'id')->toArray();

            $userIds = [];
            foreach ($receptionists as $key => $userId) {
                $userIds[$userId] = Notification::NOTIFICATION_FOR[Notification::RECEPTIONIST];
            }
            $users = getAllNotificationUser($userIds);

            foreach ($users as $key => $notification) {
                if (isset($key)) {
                    addNotification([
                        Notification::NOTIFICATION_TYPE['Patient'],
                        $key,
                        $notification,
                        $input['first_name'].' '.$input['last_name'].' added as a patient.',
                    ]);
                }
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
