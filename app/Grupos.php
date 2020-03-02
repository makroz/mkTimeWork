<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupos extends Model
{
    use Http\Controllers\Mk_ia_model;

//
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $dates = ['deleted_at'];
//
    protected $fillable = ['name', 'descrip','status'];
    protected $attributes = [
        'status' => 1,
    ];
//
    //public $_pivotes = ['permisos:permisos.id,permisos.name,permisos.status'];

    public function permisos()
    {
        return $this->belongsToMany('App\Permisos')
        ->withPivot('valor');
        // Si el nombre de la tabla es diferente a lo predeterminado o el ID de la tabla tiene otro nombre.
        //return $this->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');
    }
//
}
