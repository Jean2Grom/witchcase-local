<?php
namespace WC\Attribute;

use WC\Attribute;

class TextAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "text";
    const ELEMENTS          = [
        "value"    => "TEXT DEFAULT NULL",
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
