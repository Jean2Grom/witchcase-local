<?php
namespace WC\Attributes;

use WC\Attribute;

class StringAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "string";
    const ELEMENTS          = [
        "value" => "VARCHAR(511) DEFAULT NULL",
//"value"    => "VARCHAR(:length) DEFAULT NULL",
//"priority" => "SMALLINT DEFAULT 0", => à ajouter à Attribute ?
    ];
    const PARAMETERS        = [
//        "length"    => [
//            'type'      => "integer",
//            'default'   => 511,
//        ]
    ];
    
    
    function __construct( \WC\WitchCase $wc, $attributeName, $params=[] )
    {
        $this->name     = $attributeName;
        
        parent::__construct( $wc );
    }
    
    function content()
    {
        if( $this->values['value'] ){
            return $this->values['value'];
        }
        else {
            return false;
        }
    }
    
    static function verifyLenght( $lenght )
    {
        if( !is_numeric($lenght) ){
            return false;
        }
        
        if( strcmp($lenght, (int) $lenght) != 0 ){
            return false;
        }
        
        if( (int) $lenght <= 0 ){
            return false;
        }
        
        return true;
    }
    
    function lenghtForm( $indice )
    {
        $title  = "lenght";
        
        $inputAttributes =  [
            'name'      =>  "attributes[".$indice."][parameters][".$title."]",
            'type'      =>  false,  // Possible values : color, date, datetime, datetime-local, 
                                    // email, month, number, range, search, tel, time, url, week
            'disabled'  =>  false,  //Specifies that an input field should be disabled
            'max'       =>  false,  //Specifies the maximum value for an input field
            'maxlength' =>  false,  //Specifies the maximum number of character for an input field
            'min'       =>  false,  //Specifies the minimum value for an input field
            'pattern'   =>  false,  //Specifies a regular expression to check the input value against
            'readonly'  =>  false,  //Specifies that an input field is read only (cannot be changed)
            'required'  =>  false,  //Specifies that an input field is required (must be filled out)
            'size'      =>  false,  //Specifies the width (in characters) of an input field
            'step'      =>  false,  //Specifies the legal number intervals for an input field
            'value'     =>  false,  //Specifies the default value for an input field
        ];
        
        $inputAttributes['type']    = "text";
        $inputAttributes['value']   = $this->parameters['lenght']['value'];
        
        include $this->module->getDesignFile('attributes/edit/parameters/simpleInput.php');
        
        return;
    }
    
    function searchCondition( $targetTable, $value )
    {
        return  "`".$targetTable."`.`".$this->tableColumns[ 'value' ]."` LIKE \"".$value."\" ";
    }
}
