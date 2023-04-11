<?php
namespace WC\Attribute;

use WC\Attribute;

class DecimalAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "decimal";
    const ELEMENTS          = [
        "value" => "DECIMAL(10,2) DEFAULT NULL",
    ];
    const PARAMETERS        = [];
    
    function content()
    {
        if( $this->values['value'] ){
            return $this->values['value'];
        }
        else {
            return false;
        }
    }
}
