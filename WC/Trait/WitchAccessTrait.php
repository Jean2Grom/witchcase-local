<?php
namespace WC\Trait;

trait WitchAccessTrait
{
    function witch( ?string $witchName=null )
    {
        if( is_null($witchName) )
        {
            $obj = new \ReflectionObject($this);
            
            if( $obj->hasProperty('witch') ){
                return $this->witch;
            }
        }
        
        return $this->wc->cairn->witch( $witchName );
    }
}
