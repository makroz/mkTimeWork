<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    protected $fillable = ['name','email','pass', 'roles_id','status'];

    public $_validators =[
        'name' => 'required',
        'email' => 'required|email',
        'pass' => 'sometimes|required|min:8',
        'roles_id' => 'integer',
        'status' => 'in:0,1'
    ];

    protected $attributes = [
        'status' => 1,
    ];

    protected $hidden = ['pass'];


    public $_relaciones = ['grupos:grupos.id'];

    use Http\Controllers\Mk_ia_model;

    public function permisos()
    {
        return $this->belongsToMany('App\Permisos', 'usuarios_permisos')
        ->withPivot('valor');
    }

    public function grupos()
    {
        return $this->belongsToMany('App\Grupos', 'usuarios_grupos')
        ->withPivot('grupos_id');
    }

    public function roles()
    {
        return $this->hasMany('App\Roles');
    }
}
