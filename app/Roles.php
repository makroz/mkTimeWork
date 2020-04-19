<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use Http\Controllers\Mk_ia_model;
//
    protected $fillable = ['name', 'descrip','status'];
    protected $attributes = ['status' => 1,];
    public function getRules($request){
        return [
        'name' => 'required',
        'status' => 'in:0,1'
        ];
    }


}
