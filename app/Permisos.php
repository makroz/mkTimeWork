<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permisos extends Model
{
    use Http\Controllers\Mk_ia_model;

    protected $fillable = ['name','slug', 'descrip','status'];
    protected $attributes = ['status' => 1];
    protected $cascadeDeletes = ['usuarios','grupos'];

    public $_validators =[
        'name' => 'required',
        'slug' => 'required',
        'status' => 'in:0,1'
    ];

    public function usuarios()
    {
        return $this->belongsToMany('App\Usuarios', 'usuarios_permisos')
        ->withPivot('valor');
    }
    public function grupos()
    {
        return $this->belongsToMany('App\Grupos', 'grupos_permisos')
        ->withPivot('valor');
    }


}
