<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dietitian extends Model
{
    use HasFactory;

    protected $table = 'dietitian';

    public $fillable = [
         'user_id'
    ];
}
