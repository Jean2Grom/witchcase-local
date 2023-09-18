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
    
}
