<?php
namespace WC\Attribute;

/**
 * Class to handle Text Attributes
 * 
 * @author Jean2Grom
 */
class TextAttribute extends \WC\Attribute 
{
    const ATTRIBUTE_TYPE    = "text";
    const ELEMENTS          = [
        "value"    => "TEXT DEFAULT NULL",
    ];
    const PARAMETERS        = [];    
}
