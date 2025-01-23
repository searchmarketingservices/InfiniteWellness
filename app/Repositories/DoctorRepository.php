<?php

namespace App\Repositories;

use Arr;
use Hash;
use Exception;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Address;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;


class DoctorRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'doctor_user_id',
        'specialist',
    ];


    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }


    public function model()
    {
        return Doctor::class;
    }


    // public function store($input, $mail = false)
    // {
    //     try {
    //         $input['phone'] = preparePhoneNumber($input, 'phone');
    //         $input['password'] = Hash::make($input['password']);
    //         $input['dob'] = (! empty($input['dob'])) ? $input['dob'] : null;
    //         $user = User::create(Arr::except($input, ['specialist']));
    //         if ($mail) {
    //             $user->sendEmailVerificationNotification();
    //         }

    //         if (isset($input['image']) && ! empty($input['image'])) {
    //             $mediaId = storeProfileImage($user, $input['image']);
    //         }

    //         $doctor = Doctor::create([
    //             'doctor_user_id' => $user->id,
    //             'department_id' => $input['department_id'],

    //             'specialist' => $input['specialist'],
    //         ]);
    //         $ownerId = $doctor->id;
    //         $ownerType = Doctor::class;

    //         if (! empty($address = Address::prepareAddressArray($input))) {
    //             Address::create(array_merge($address, ['owner_id' => $ownerId, 'owner_type' => $ownerType]));
    //         }

    //         $user->update(['owner_id' => $ownerId, 'owner_type' => $ownerType]);
    //         $user->assignRole($input['department_id']);
    //     } catch (Exception $e) {
    //         throw new UnprocessableEntityHttpException($e->getMessage());
    //     }

    //     return true;
    // }


    public function store($input, $mail = false)
    {
        try {
            // Validate and prepare phone number
            $input['phone'] = preparePhoneNumber($input, 'phone');
            // Hash password
            $input['password'] = Hash::make($input['password']);
            // Handle date of birth
            $input['dob'] = !empty($input['dob']) ? $input['dob'] : null;

            // Create user without 'specialist' and 'department_id'

            $user = User::create(Arr::except($input, ['specialist']));
            // Send email verification if needed
            if ($mail) {
                $user->sendEmailVerificationNotification();
            }

            // Handle image upload if provided
            if (!empty($input['image'])) {
                storeProfileImage($user, $input['image']);
            }

            // Create doctor and associate with user
            $doctor = Doctor::create([
                'doctor_user_id' => $user->id,
                'department_id' => $input['department_id'],
                'specialist' => $input['specialist'],
            ]);

            // Save address if available
            if (!empty($address = Address::prepareAddressArray($input))) {
                Address::create(array_merge($address, [
                    'owner_id' => $doctor->id,
                    'owner_type' => Doctor::class,
                ]));
            }

            // Update user owner ID and type
            $user->update([
                'owner_id' => $doctor->id,
                'owner_type' => Doctor::class,
            ]);

            // Assign role to user (based on your actual role logic)
            $user->assignRole('Doctor');
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }

    public function update($doctor, $input)
    {
        try {
            unset($input['password']);
            $user = User::find($doctor->doctorUser->id);
            if ($input['avatar_remove'] == 1 && isset($input['avatar_remove']) && ! empty($input['avatar_remove'])) {
                removeFile($user, User::COLLECTION_PROFILE_PICTURES);
            }
            if (isset($input['image']) && ! empty($input['image'])) {
                $mediaId = updateProfileImage($user, $input['image']);
            }


            $input['phone'] = preparePhoneNumber($input, 'phone');
            $input['dob'] = (! empty($input['dob'])) ? $input['dob'] : null;
            $doctor->doctorUser->update($input);
            $doctor->update($input);

            if (! empty($doctor->address)) {
                if (empty($address = Address::prepareAddressArray($input))) {
                    $doctor->address->delete();
                }
                $doctor->address->update($input);
            } else {
                if (! empty($address = Address::prepareAddressArray($input)) && empty($doctor->address)) {
                    $ownerId = $doctor->id;
                    $ownerType = Doctor::class;
                    Address::create(array_merge($address, ['owner_id' => $ownerId, 'owner_type' => $ownerType]));
                }
            }
            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }


    public function getDoctors()
    {

        $doctors = Doctor::with('doctorUser')->get()->where('doctorUser.status', '=', 1)->pluck('doctorUser.full_name',
            'id')->sort();

        return $doctors;
    }


    public function getDoctorAssociatedData($doctorId)
    {
        $data['doctorData'] = Doctor::with([
            'cases.patient.patientUser', 'patients.patientUser', 'schedules', 'payrolls', 'doctorUser',
            'address', 'appointments.doctor.doctorUser', 'appointments.patient.patientUser', 'appointments.department',
        ])->findOrFail($doctorId);
        if (! $data['doctorData']) {
            return false;
        }
        $data['appointments'] = $data['doctorData']->appointments;

        return $data;
    }
}
