<?php

namespace App\modulos\mkUsuarios;

use Illuminate\Database\Eloquent\Model;
use \App\modulos\mkBase\Mk_ia_model;

class Roles extends Model
{
    use Mk_ia_model;
//
    protected $fillable = ['name', 'descrip','status'];
    protected $attributes = ['status' => 1,];
    public function getRules($request){
        return [
        'name' => 'required_with:name',
        'status' => 'in:0,1'
        ];
    }


}
