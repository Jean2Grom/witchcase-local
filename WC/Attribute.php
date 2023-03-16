<?php
namespace WC;

abstract class Attribute 
{
    const CONTROLLER_SUBFOLDER          = "controller/attributes";
    const DESIGN_SUBFOLDER              = "design/attributes";

    var $parameters     = [];
    var $dbFields       = [];
    var $values         = [];
    var $tableColumns   = [];
    var $joinTables     = [];
    var $joinFields     = [];
    var $joinConditions = [];
    var $leftJoin       = [];
    var $groupBy        = [];
    
    var $name;
    var $type;
    
    /** WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc )
    {
        $this->wc                   = $wc;
        $instanciedAttributeClass   = (new \ReflectionClass($this))->getName();
        $this->type                 = $instanciedAttributeClass::ATTRIBUTE_TYPE;
        
        foreach( $instanciedAttributeClass::ELEMENTS as $elementKey => $elementValue )
        {
            $this->tableColumns[ $elementKey ]  = self::getColumnName( $this->type, $this->name, $elementKey );
            $this->dbFields[ $elementKey ]      = "`".$this->tableColumns[ $elementKey ]."` ".$elementValue;
            $this->values[ $elementKey ]        = NULL;
        }
    }
    
    function set( $args )
    {
        foreach( $args as $key => $value ){
            $this->values[ $key ] = $value;
        }
        
        return $this;
    }
    
    function content()
    {
        if( !is_array($this->values) ){
            return $this->values;
        }
        elseif( count($this->values) == 1 ){
            return array_values($this->values)[0];
        }
        else {
            return $this->values;
        }
    }
    
    function setValue( $key, $value )
    {
        $this->values[ $key ] = $value;
        
        return $this;
    }
    
    function display( $filename=false )
    {
        if( !$filename ){
            $filename = strtolower( $this->type );
        }
        
        $file = $this->wc->website->getFilePath( self::CONTROLLER_SUBFOLDER."/view/".$filename.'.php');
        
        if( !$file ){
            $file = $this->wc->website->getFilePath( self::CONTROLLER_SUBFOLDER."/view/default.php");
        }
        
        if( $file ){
            include $file;
        }
        
        return;
    }
    
    function edit( $filename=false )
    {
        if( !$filename ){
            $filename = strtolower( $this->type );
        }
        
        $file = $this->wc->website->getFilePath( self::CONTROLLER_SUBFOLDER."/edit/".$filename.'.php');
        
        if( !$file ){
            $file = $this->wc->website->getFilePath( self::CONTROLLER_SUBFOLDER."/edit/default.php");
        }
        
        if( $file ){
            include $file;
        }
        
        return true;
    }
    
    function save( $target )
    {
        return $this;
    }
    
    function delete()
    {
        return true;
    }
    
    static function getColumnName( $type, $name, $element )
    {
        return "@_".$type."#".$element."__".$name;
    }
    
    static function splitColumn( $columnName )
    {
        if( strcmp(substr($columnName, 0, 2), "@_") != 0 ){
            return false;
        }
        
        $buffer         = explode("__", substr($columnName, 2));
        $attributeType  = $buffer[0];
        $attributeName  = $buffer[1];
        
        $attributeElement = false;
        if( strstr($attributeType, "#") )
        {
            $buffer = explode("#", $attributeType);
            $attributeType      = $buffer[0];
            $attributeElement   = $buffer[1];
        }
        
        return  [
            'name'      =>  $attributeName,
            'type'      =>  $attributeType,
            'element'   =>  $attributeElement
        ];
    }
    
    
}
