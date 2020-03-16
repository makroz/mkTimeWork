<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    protected $fillable = ['name','email','pass', 'roles_id','status'];

    protected $attributes = [
        'status' => 1,
    ];

    protected $hidden = ['pivot'];


    public $_relaciones = ['grupos:grupos.id'];

    use Http\Controllers\Mk_ia_model;

    public function permisos()
    {
        return $this->belongsToMany('App\Permisos', 'usuarios_permisos')
        ->withPivot('valor');
        // Si el nombre de la tabla es diferente a lo predeterminado o el ID de la tabla tiene otro nombre.
        //return $this->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');
    }

    public function grupos()
    {
        return $this->belongsToMany('App\Grupos', 'usuarios_grupos')
        ->withPivot('grupos_id');
        // Si el nombre de la tabla es diferente a lo predeterminado o el ID de la tabla tiene otro nombre.
        //return $this->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');
    }

    public function roles()
    {
        return $this->hasMany('App\Roles');
    }
}
