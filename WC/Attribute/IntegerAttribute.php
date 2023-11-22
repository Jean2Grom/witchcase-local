<?php
namespace WC\Attribute;

/**
 * Class to handle Integer Attributes
 * 
 * @author Jean2Grom
 */
class IntegerAttribute extends \WC\Attribute 
{
    const ATTRIBUTE_TYPE    = "integer";
    const ELEMENTS          = [
        "value" => "INT(11) DEFAULT NULL",
    ];
    const PARAMETERS        = [];    
}
