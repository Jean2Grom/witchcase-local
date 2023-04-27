<?php
namespace WC;

abstract class Attribute 
{
    const ATTRIBUTE_TYPE                = null;
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
    
    function __construct( WitchCase $wc, string $name, array $parameters=[] )
    {
        $this->wc                   = $wc;
        $this->name                 = $name;
        $this->parameters           = $parameters;
        
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
        return 0;
    }
    
    function delete()
    {
        return true;
    }
    
    static function getColumnName( string $type, string $name, string $element ): string
    {
        return $name."@".$type."#".$element;
    }
    
    static function splitColumn( string $columnName )
    {
        $buffer         = explode( "@", trim($columnName) );
        $attributeName  = $buffer[0];
        
        if( isset($buffer[1]) )
        {
            $buffer             = explode( "#", $buffer[1] );
            $attributeType      = $buffer[0];
            $attributeElement   = $buffer[1];
        }
        
        return  [
            'name'      =>  $attributeName,
            'type'      =>  $attributeType ?? false,
            'element'   =>  $attributeElement ?? false,
        ];
    }
    
    function getJointure( string $fromTable ): array
    {
        $targetTable        = trim( $this->wc->db->escape_string($fromTable) );
        $jointuresParams    = [ 'target_table' => $targetTable ];
        foreach( $this->joinTables as $joinTableData ){
            $jointuresParams[ $joinTableData['table'] ] = $joinTableData['table'].'|'.$targetTable.'@'.$this->name;
        }
        
        $jointures          = [];
        foreach( $this->joinTables as $joinTableData )
        {
            $jointureQueryPart  =   "LEFT JOIN ";
            $jointureQueryPart  .=  "`".$joinTableData['table']."` AS `".$jointuresParams[ $joinTableData['table'] ]."` ";
            $jointureQueryPart  .=  "ON ";
            $jointureQueryPart  .=  str_replace(
                                        array_map( fn($key): string => ':'.$key, array_keys($jointuresParams) ), 
                                        array_map( fn($value): string => "`".$value."`", array_values($jointuresParams) ), 
                                        $joinTableData['condition']
                                    );
            $jointureQueryPart  .=  " ";
            
            $jointures[] = $jointureQueryPart;
        }
        
        return $jointures;
    }
    
    function getJoinFields( string $fromTable ): array
    {
        $targetTable        = trim( $this->wc->db->escape_string($fromTable) );
        $jointuresParams    = [ 'target_table' => $targetTable ];
        foreach( $this->joinTables as $joinTableData ){
            $jointuresParams[ $joinTableData['table'] ] = $joinTableData['table'].'|'.$targetTable.'@'.$this->name;
        }
        
        $joinFields = [];
        foreach( $this->joinFields as $joinField )
        {
            $field =    str_replace(
                            array_map( fn($key): string => ':'.$key, array_keys($jointuresParams) ), 
                            array_map( fn($value): string => "`".$value."`", array_values($jointuresParams) ), 
                            $joinField
                        );
            
            $joinFields[] = str_replace("`|", "|", $field)." ";
        }
        
        return $joinFields;
    }
    
    function getSelectFields( string $fromTable ): array
    {
        $querySelectFields = [];
        foreach( $this->tableColumns as $attributeElement => $attributeElementColumn )
        {
            $field  =   "`".$fromTable."`.`".$attributeElementColumn."` ";
            $field  .=  "AS `".$fromTable."|".$this->name;
            $field  .=  "#".$attributeElement."` ";

            $querySelectFields[] = $field;
        }
        
        array_push( $querySelectFields, ...$this->getJoinFields($fromTable) );
        
        return $querySelectFields;
    }
    
    static function splitSelectField( string $fieldName )
    {
        $buffer         = explode('|', $fieldName);
        $table          = $buffer[0];
        
        if( isset($buffer[ 1 ]) )
        {
            $subBuffer      = explode('#', $buffer[1]);
            $field          = $subBuffer[0];
            $fieldElement   = $subBuffer[1] ?? false;
        }
        
        return  [
            'table'     =>  $table,
            'field'     =>  $field,
            'element'   =>  $fieldElement,
        ];
    }
    
    static function list( array $extendDirs=[] )
    {
        $dirs = array_unique(array_merge($extendDirs, [__DIR__.'/Attribute']));
        
        $attributesList                 = [];
        $attributeNameSpaceClassPrefix  = __CLASS__."\\";
        $attributeNameSpaceClassSuffix  = "Attribute";
        
        foreach( $dirs as $dir ){
            foreach( scandir($dir) as $file ){
                if( substr($file, -(strlen($attributeNameSpaceClassSuffix)+4), -4 ) == $attributeNameSpaceClassSuffix )
                {
                    $className = substr($attributeNameSpaceClassPrefix.$file, 0, -4);
                    if( !empty($className::ATTRIBUTE_TYPE) ){
                        $attributesList[ $className::ATTRIBUTE_TYPE ] =  $className;            
                    }
                }
            }
        }
        
        return $attributesList;        
    }
    
    function searchCondition( string $targetTable, mixed $value )
    {
        if( $this->tableColumns['value'] )
        {
            $key = md5($targetTable.$this->tableColumns[ 'value' ].$value);
            return  [
                'query'     => "`".$this->wc->db->escape_string($targetTable)."`.`".$this->tableColumns[ 'value' ]."` = :".$key." ",
                'params'    => [ $key => $value ],
            ];
        }
        
        return false;
    }
    
    function getEditParams(): array
    {
        return array_values($this->tableColumns);
    }
    
    function update( array $params )
    {
        foreach( $params as $key => $value )
        {
            $data = self::splitColumn($key);
            
            if( $data['type'] == $this->type 
                && $data['name'] == $this->name 
                && in_array($data['element'], array_keys($this->values)) 
            ){
                $this->values[ $data['element'] ] = $value;
            }            
        }
    }
}
