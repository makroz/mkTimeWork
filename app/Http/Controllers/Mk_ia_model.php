<?php
namespace App\Http\Controllers;

trait Mk_ia_model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public function getFill()
    {
        return $this->fillable;
    }


    public function toArray()
    {
        //TODO: llevar esto dentro del traid o del auth donde corresponda
        $attributes = $this->attributesToArray();
        $attributes = array_merge($attributes, $this->relationsToArray());

        if (isset($attributes['grupos'])) {
            if (isset($attributes['gruposid'])) {
                $i=$attributes['gruposid'];
            } else {
                $i=[];
            }
            foreach ($attributes['grupos'] as $key => $value) {
                $i[]=$value['id'];
            }
            $attributes['gruposid'] = $i;
            unset($attributes['grupos']);
        }

        unset($attributes['pivot']);
        return $attributes;
    }
}
