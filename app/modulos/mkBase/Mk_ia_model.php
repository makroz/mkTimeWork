<?php
namespace App\modulos\mkBase;

use App\modulos\mkBase\Mk_helpers\Mk_debug;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;

trait Mk_ia_model
{
    use SoftDeletes;

    public function getFill()
    {
        return $this->fillable;
    }
    public function getRules($request){
        return null;
    }

    public function toArray()
    {
        $attributes = $this->attributesToArray();
        $attributes = array_merge($attributes, $this->relationsToArray());
        if (!empty($this->_withRelations)){
            foreach ($this->_pivot2Array as $key1 => $value1) {
                $dat=explode(':',$value1.':id');
                $rel=$dat[0];
                $piv=$dat[1];
                $dat=explode('.','.'.$piv);
                $piv=$dat[count($dat)-1];

                if (isset($attributes[$rel])) {
                    $i=[];
                    foreach ($attributes[$rel] as $key => $value) {
                        $i[]=$value[$piv];
                    }
                    $attributes[$rel] = $i;
                }
            }
        }
        return $attributes;
    }

//****softdelete cascade ***
    public function runCascadingDeletes($ids,$restore=false)
    {
        if ($invalidCascadingRelationships = $this->hasInvalidCascadingRelationships()) {
            throw CascadeSoftDeleteException::invalidRelationships($invalidCascadingRelationships);
        }
        foreach ($this->getActiveCascadingDeletes() as $relationship) {
            $this->cascadeSoftDeletes($relationship,$ids,$restore);
        }
    }

    protected function cascadeSoftDeletes($relationship,$ids,$restore=false)
    {
        $dato=$this->fromDateTime($this->freshTimestamp());
        if ($restore){
            $dato=null;
        }
        $table=$this->{$relationship}()->getTable();
        $id=$this->{$relationship}()->getForeignPivotKeyName();
        DB::table($table)
               ->whereIn($id, $ids)
               ->update([$this->getDeletedAtColumn() =>  $dato]);
    }

    protected function hasInvalidCascadingRelationships()
    {
        return array_filter($this->getCascadingDeletes(), function ($relationship) {
            return  !method_exists($this, $relationship) ||  !$this->{$relationship}() instanceof Relation;
        });
    }

    protected function getCascadingDeletes()
    {
        return isset($this->cascadeDeletes) ? (array) $this->cascadeDeletes : [];
    }

    protected function getActiveCascadingDeletes()
    {
        return array_filter($this->getCascadingDeletes(), function ($relationship) {
            return ! is_null($this->{$relationship});
        });
    }
}
