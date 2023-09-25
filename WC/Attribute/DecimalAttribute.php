<?php
namespace WC\Attribute;

class DecimalAttribute extends \WC\Attribute 
{
    const ATTRIBUTE_TYPE    = "decimal";
    const ELEMENTS          = [
        "value" => "DECIMAL(10,2) DEFAULT NULL",
    ];
    const PARAMETERS        = [];    
}
