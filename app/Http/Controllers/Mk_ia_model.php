<?php
namespace App\Http\Controllers;

trait Mk_ia_model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public function getFill()
    {
        return $this->fillable;
    }

}
