<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;

class FunctionalMedicine extends Model
{
    use HasFactory;
    protected $table = 'functional_medicine';
    protected $fillable = [
        'id', 'patient_id',
        'help', 'life_line', 'food', 'intellectual', 'job_work', 'leisure', 'physical', 'relationship', 'social', 'spritual', 'interpretation', 'examination', 'investigation',
        'details', 'created_at', 'updated_at',
        'nutrition', 'aerobics', 'balance', 'strength', 'schedule_sleep', 'quality_sleep', 'enivronment_sleep',
        'attitude', 'stress', 'social_connection', 'seeking_help',

        'alcohol', 'smoking', 'abuse',
        'clean', 'safety',
        'leisure_activities', 'family', 'social_time', 'time_management',
        'intermittent','essential_herbs'
    ];

        public function patient()
        {
            return $this->belongsTo(Patient::class);
        }
}
