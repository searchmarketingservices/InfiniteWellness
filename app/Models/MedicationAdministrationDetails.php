<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationAdministrationDetails extends Model
{
    use HasFactory;

    protected $table = "medication_administration_details";
    protected $fillable = [
        'medication_administration_id',
        'drug_name',
        'dose',
        'route',
        'staff_name',
        'created_at',
        'updated_at',
    ];
}
