<?php
namespace WC;

class Session 
{
    var $namespace;
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc, string $namespace="" )
    {
        $this->wc           = $wc;
        
        if( session_status() !== PHP_SESSION_ACTIVE ){
            session_start();
        }
        
        if( !empty($namespace) ){
            $this->namespace = $namespace;
        }
        else {
            $this->namespace = $this->wc->website->name;
        }
        
        if( empty($_SESSION[ $this->namespace ]) ){
            $_SESSION[ $this->namespace ] = [];
        }
    }
    
    function write( string $name, mixed $value ): self
    {        
        if( is_object($value) )
        {
            $value = serialize($value);
            $_SESSION[ $this->namespace ][ 'wcObjectsHashArray' ] = array_replace(
                $_SESSION[ $this->namespace ][ 'wcObjectsHashArray' ] ?? [],
                [ $name => hash_hmac('sha256', $value, session_id()) ]
            );
        }
        
        $_SESSION[ $this->namespace ][ $name ] = $value;
        
        return $this;
    }    
    
    function read( string $name ): mixed
    {
        $value      = $_SESSION[ $this->namespace ][ $name ] ?? false;
        $objectHash = $_SESSION[ $this->namespace ][ 'wcObjectsHashArray' ][ $name ] ?? false;
        
        if( $objectHash && hash_hmac('sha256', $value, session_id()) === $objectHash ){
            return unserialize($value);
        }
        
        return $value;
    }
    
    function delete( string $name ): self
    {
        $_SESSION[ $this->namespace ][ 'wcObjectsHashArray' ][ $name ]    = null;
        $_SESSION[ $this->namespace ][ $name ]                            = null;
        
        return $this;
    }
    
    function destroy(): self
    {
        $_SESSION[ $this->namespace ] = null;
        
        return $this;
    }
    
    function kill(): self
    {
        session_unset();
        
        return $this;
    }
    
    function resetAll(): self
    {
        session_destroy();
        
        return $this;
    }
    
    function pushTo( string $name, mixed $value ): self
    {
        $array = $_SESSION[ $this->namespace ][ $name ] ?? [];
        
        if( !is_array($array) ){
            $array = [ $array ];
        }
        
        $array[] = $value;
        
        $_SESSION[ $this->namespace ][ $name ] = $array;
        
        return $this;
    }
    
    function mergeTo( string $name, array $value ): self
    {
        $array = $_SESSION[ $this->namespace ][ $name ] ?? [];
        
        if( !is_array($array) ){
            $array = [ $array ];
        }
        
        $_SESSION[ $this->namespace ][ $name ] = array_replace_recursive($array, $value);
        
        return $this;
    }

    function removeFrom( string $name, mixed $value ): self
    {
        $array = $_SESSION[ $this->namespace ][ $name ] ?? [];
        
        if( !is_array($array) ){
            $array = [ $array ];
        }
        
        $newArray = [];
        foreach( $array as $arrayItem ){
            if( $value !== $arrayItem ){
                $newArray[] = $arrayItem;
            }
        }
        unset($array);        
        
        $_SESSION[ $this->namespace ][ $name ] = $newArray;
        
        return $this;
    }    
}
