<?php
namespace WC\Trait;

use WC\Witch;

trait ShortcutAccessTrait
{
    function __call( $name, $arguments )
    {
        $callable = [$this->wc, $name];
        if( !is_callable($callable, false) )
        {
            $trace = debug_backtrace();
            $this->wc->log->error(  
                __CLASS__.": Unidentified Method call \"".$name.'"',
                true, 
                [
                    'file' => $trace[0]['file'], 
                    'line' => $trace[0]['line'] 
                ]
            );
        }

        return call_user_func_array( [$this->wc, $name], $arguments );
    }


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
