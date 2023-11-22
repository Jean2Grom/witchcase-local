<?php
namespace WC\Attribute;

/**
 * Class to handle Decimal Attributes
 * 
 * @author Jean2Grom
 */
class DecimalAttribute extends \WC\Attribute 
{
    const ATTRIBUTE_TYPE    = "decimal";
    const ELEMENTS          = [
        "value" => "DECIMAL(10,2) DEFAULT NULL",
    ];
    const PARAMETERS        = [];    
}
