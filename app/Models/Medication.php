<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $table = 'medications';
    protected $fillable = [
        'nursing_form_id',
        'patient_mr_number',
        'medication_name',
        'dosage',
        'frequency',
        'root',
        'prescribing_physician',
    ];
}
