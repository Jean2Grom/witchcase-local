<?php
namespace WC\Attributes;

use WC\Attribute;

class TextAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "text";
    const ELEMENTS          = [
        "value"    => "TEXT DEFAULT NULL",
    ];
    const PARAMETERS        = [];
    
    function __construct( $module, $attributeName, $params=[] )
    {
        $this->name     = $attributeName;
        
        parent::__construct( $module );
    }
    
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
