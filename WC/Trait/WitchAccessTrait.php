<?php
namespace WC\Trait;

use WC\Witch;

trait WitchAccessTrait
{
    function witch( ?string $witchName=null ): ?Witch
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
