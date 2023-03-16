<?php
namespace WC\Attributes;

use WC\Attribute;

class BooleanAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "boolean";
    const ELEMENTS          = [
        "value" => "INT(1) DEFAULT NULL",
    ];
    const PARAMETERS        = [];
    
    function __construct( \WC\WitchCase $wc, $attributeName, $params=[] )
    {
        $this->name     = $attributeName;
        
        parent::__construct( $wc );
    }
    
    function content()
    {
        if( is_null($this->values['value']) ){
            return NULL;
        }
        
        return (boolean) $this->values['value'];
    }
    
}
