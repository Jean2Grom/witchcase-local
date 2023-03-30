<?php
namespace WC\Attributes;

use WC\Attribute;

class DateTimeAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "datetime";
    const ELEMENTS          = [
        "value"    => "DATETIME DEFAULT NULL",
    ];
    const PARAMETERS        = [];
        
    function content()
    {
        if( $this->values['value'] 
                && $this->values['value'] != "0000-00-00 00:00:00" ){
            return new \DateTime( $this->values['value'] );
        }
        
        return false;
    }
}
