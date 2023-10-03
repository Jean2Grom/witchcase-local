<?php
namespace WC\Attribute;

class IntegerAttribute extends \WC\Attribute 
{
    const ATTRIBUTE_TYPE    = "integer";
    const ELEMENTS          = [
        "value" => "INT(11) DEFAULT NULL",
    ];
    const PARAMETERS        = [];    
}
