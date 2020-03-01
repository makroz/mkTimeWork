<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'descrip','status'];

    public function getFill()
    {
        return $this->fillable;
    }
}
