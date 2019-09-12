<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permisos extends Model
{
    protected  $fillable = ['name','fk_grupospermisos'];
}
