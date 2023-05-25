<?php
namespace WC\Attribute;

use WC\Attribute;
use WC\Datatype\ExtendedDateTime;

class DatetimeAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "datetime";
    const ELEMENTS          = [
        "value"    => "DATETIME DEFAULT NULL",
    ];
    const PARAMETERS        = [];
        
    function content()
    {
        if( $this->values['value'] 
                && $this->values['value'] != "0000-00-00 00:00:00" ){
            return new \DateTime( $this->values['value'] );
        }
        
        return false;
    }
    
    function update( array $params )
    {
        $key = $this->tableColumns['value'];
        
        if( !empty($params[ $key ]) )
        {
            $value = new ExtendedDateTime( $params[ $key ] );
            
            
            $this->values['value'] = $value->sqlFormat();
        }
    }    
}
