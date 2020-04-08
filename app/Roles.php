<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use Http\Controllers\Mk_ia_model;
//
    protected $fillable = ['name', 'descrip','status'];
    public $_validators =[
        'name' => 'required',
        'status' => 'in:0,1'
    ];

    protected $attributes = [
        'status' => 1,
    ];
}
