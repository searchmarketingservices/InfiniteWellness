<?php

namespace App\Models;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use App\Models\MedicationAdministrationDetails;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicattionAdministration extends Model
{
    use HasFactory;
    protected $table = "medication_administration";

    protected $fillable = [
        'mr_number',
        'date',
        'time',
        'patient_name',
        'bp',
        'heart_rate',
        'respiratory_rate',
        'temperature',
        'spo_2',
        'note',
        'created_at',
        'updated_at'
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class,'mr_number','MR');
    }

    public function Medication(): HasMany
    {
        return $this->hasMany(MedicationAdministrationDetails::class , 'medication_administration_id', 'id');
    }

}
