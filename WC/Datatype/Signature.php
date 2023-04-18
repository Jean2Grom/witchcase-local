<?php
namespace WC\Datatype;

class Signature 
{
    
    function __construct( $name, $id, $value )
    {
        $this->name     = $name;
        $this->id       = $id;
        $this->value    = $value;
    }
    
}
