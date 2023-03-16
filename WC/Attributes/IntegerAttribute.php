<?php
namespace WC\Attributes;

use WC\Attribute;

class IntegerAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "integer";
    const ELEMENTS          = [
        "value" => "INT(11) DEFAULT NULL",
    ];
    const PARAMETERS        = [];
    
    function __construct( \WC\WitchCase $wc, $attributeName, $params=[] )
    {
        $this->name     = $attributeName;
        
        parent::__construct( $wc );
    }
}
