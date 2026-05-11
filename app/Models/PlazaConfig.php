<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlazaConfig extends Model
{
    protected $table = 'tbplaza_config';
    protected $primaryKey = 'plaza_config_id';
    
    protected $fillable = ['latitude', 'longitude', 'radius_meters'];
}
