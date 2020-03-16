<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permisos extends Model
{
    use Http\Controllers\Mk_ia_model;
//
    protected $fillable = ['name','slug', 'descrip','status'];
    protected $attributes = [
        'status' => 1,
    ];
}
