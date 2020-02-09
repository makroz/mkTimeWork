<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupos_permisos extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;


    protected $dates = ['deleted_at'];

    protected $fillable = ['name'];
}
