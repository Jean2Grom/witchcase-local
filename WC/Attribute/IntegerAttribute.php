<?php
namespace WC\Attribute;

use WC\Attribute;

class IntegerAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "integer";
    const ELEMENTS          = [
        "value" => "INT(11) DEFAULT NULL",
    ];
    const PARAMETERS        = [];    
}
