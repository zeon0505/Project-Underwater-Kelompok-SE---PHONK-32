<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterQualityLog extends Model
{
    protected $fillable = ['ph', 'temperature', 'tds', 'turbidity', 'ec', 'do', 'drone_id'];
}
