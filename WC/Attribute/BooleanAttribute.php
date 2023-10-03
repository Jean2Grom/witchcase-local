<?php
namespace WC\Attribute;

class BooleanAttribute extends \WC\Attribute  
{
    const ATTRIBUTE_TYPE    = "boolean";
    const ELEMENTS          = [
        "value" => "INT(1) DEFAULT NULL",
    ];
    const PARAMETERS        = [];
        
    function content( ?string $element=null )
    {
        if( !is_null($element) && $element !== 'value' ){
            return false;
        }
        
        if( is_null($this->values['value']) ){
            return null;
        }
        
        return (boolean) $this->values['value'];
    }
}
