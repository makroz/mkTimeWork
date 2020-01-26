<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_netpizza extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;


    protected $fillable = ['fecha','ip','ruta','user','user_desc','fk_user','comando','suc'];
}
