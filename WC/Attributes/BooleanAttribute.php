<?php
namespace WC\Attributes;

use WC\Attribute;
use WC\WitchCase;

class BooleanAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "boolean";
    const ELEMENTS          = [
        "value" => "INT(1) DEFAULT NULL",
    ];
    const PARAMETERS        = [];
        
    function content()
    {
        if( is_null($this->values['value']) ){
            return NULL;
        }
        
        return (boolean) $this->values['value'];
    }
    
}
