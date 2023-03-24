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
        $value      = $_SESSION[ $this->namespace ][ $name ];
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
    
    function addTo( string $name, mixed $value ): self
    {
        $array = $_SESSION[ $this->namespace ][ $name ] ?? [];
        
        if( !is_array($array) ){
            $array = [ $array ];
        }
        
        if( !is_array($value) || array_keys($array) === range(0, count($array) - 1) ){
            $array[] = $value;
        }
        else {
            $array = array_replace_recursive($array, $value);
        }
        
        $_SESSION[ $this->namespace ][ $name ] = $array;
        
        return $this;
    }

    function removeFrom( string $name, mixed $value ): self
    {
        $array = $_SESSION[ $this->namespace ][ $name ] ?? [];
        
        if( !is_array($array) ){
            $array = [ $array ];
        }
        
        $flippedArray = array_flip($array);
        if( isset($flippedArray[ $value ]) ){
            unset($flippedArray[ $value ]);
        }
        $newArray = array_flip($flippedArray);
        
        if( array_keys($array) === range(0, count($array) - 1) ){
            $newArray = array_values($newArray);
        }
        
        $_SESSION[ $this->namespace ][ $name ] = $newArray;
        
        return $this;
    }

    /*
    function getAlerts()
    {
        $alerts = [];
        if( !empty($_SESSION[$this->wc->website->name]['alerts']) )
        {
            $alerts = $_SESSION[$this->wc->website->name]['alerts'];
            $_SESSION[$this->wc->website->name]['alerts'] = [];
        }
        
        return $alerts;
    }
    
    function addAlerts( $newAlerts )
    {
        $alerts = $this->getAlerts();
        
        foreach( $newAlerts as $newAlertItem ){
            $alerts[] = $newAlertItem;
        }
        
        $_SESSION[$this->wc->website->name]['alerts'] = $alerts;
        
        return $this;
    }
    
    function getSessionData( $varname )
    {
        return $_SESSION[$this->wc->website->name][ $varname ] ?? NULL;
    }
    
    function setSessionData( $varname, $varvalue )
    {
        if( empty($_SESSION[ $this->wc->website->name ]) ){
            $_SESSION[ $this->wc->website->name ] = [];
        }
        
        $_SESSION[$this->wc->website->name][ $varname ] = $varvalue;
        
        return $this;
    }
    
    function addToSessionData( $varname, $varvalue )
    {
        if( empty($_SESSION[ $this->wc->website->name ]) ){
            $_SESSION[ $this->wc->website->name ] = [];
        }
        
        if( empty($_SESSION[ $this->wc->website->name ][ $varname ]) ){
            $_SESSION[ $this->wc->website->name ][ $varname ] = [];
        }
        
        $_SESSION[$this->wc->website->name][ $varname ][] = $varvalue;
        
        return $this;
    }*/
    
}
