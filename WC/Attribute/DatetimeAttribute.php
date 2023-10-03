<?php
namespace WC\Attribute;

use WC\Datatype\ExtendedDateTime;

class DatetimeAttribute extends \WC\Attribute 
{
    const ATTRIBUTE_TYPE    = "datetime";
    const ELEMENTS          = [
        "value"    => "DATETIME DEFAULT NULL",
    ];
    const PARAMETERS        = [];
        
    function content( ?string $element=null )
    {
        if( !is_null($element) && $element !== 'value' ){
            return false;
        }
        
        if( is_null($this->values['value']) || $this->values['value'] == "0000-00-00 00:00:00" ){
            return null;
        }
        
        return new \DateTime( $this->values['value'] );
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
