<?php
namespace WC;

use WC\DataAccess\Structure as StructureDA;
use WC\DataAccess\WitchDataAccess as WitchDA;
use WC\DataAccess\WitchCrafting;
use WC\Handler\WitchHandler;

use WC\Datatype\ExtendedDateTime;
use WC\Attribute;

/**
 * Class that handle Craft Structures 
 * 
 * @author Jean2Grom
 */
class Structure 
{
    public $table;
    public $type;
    public $name;
    public $attributes;    
    public $lastModified;
    public $exist;
    
    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;
    
    function __construct( WitchCase $wc, string $structureOrTableName, ?string $forcedType=null )
    {
        $this->wc = $wc;
        
        foreach( Craft::TYPES as $type ){
            if( str_starts_with($structureOrTableName, $type.'__') )
            {
                $this->table    = $structureOrTableName;
                $this->type     = $type;
                $this->name     = substr( $structureOrTableName, strlen($type.'__') );                
                break;
            }
        }
        
        if( !$this->table )
        {
            $this->name     = $structureOrTableName;
            $this->type     = Craft::TYPES[0];
            $this->table    = $this->type.'__'.$this->name;
        }
        
        if( $forcedType && in_array($forcedType, Craft::TYPES) )
        {
            $this->type     = $forcedType;
            $this->table    = $this->type.'__'.$this->name;
        }
    }
                
    function __toString() {
        return $this->name.($this->type !==  Craft::TYPES[0]? ' ['.$this->type.']': '' );
    }
    
    
    function attributes( string $requiredType=null, bool $forceReading=false )
    {
        $type = $requiredType ?? $this->type;
        
        if( isset($this->attributes[ $type ]) && !$forceReading ){
            return  $this->attributes[ $type ];
        }
        
        $table      = $type.'__'.$this->name;        
        $columns    = StructureDA::readTableStructure($this->wc, $table);
        
        $attributes   = [];
        foreach( array_keys($columns) as $columnName )
        {
            if( strpos($columnName, '@') === false ){
                continue;
            }
            
            $splitColumn = Attribute::splitColumn($columnName);
            
            $attributeName      = $splitColumn['name'];
            $attributeType      = $splitColumn['type'];            
            $attributeElement   = $splitColumn['element'];
            
            if( !$attributeType ){
                continue;
            }
            
            if( empty($attributes[ $attributeName ]) )
            {
                $className = "WC\\Attribute\\".ucfirst($attributeType).'Attribute';
                
                if( !class_exists($className) )
                {
                    $this->wc->debug( "Attribute ".$attributeName." : class not found \"".$className."\", skip" );
                    continue;
                }
                
                $attributes[ $attributeName ] = [
                    'class'     => $className,
                    'elements'  => [],
                ];
            }
            
            if( empty($attributeElement) ){
                $attributes[ $attributeName ]['elements'][ $attributeType ]       = $columnName;
            }
            else {
                $attributes[ $attributeName ]['elements'][ $attributeElement ]    = $columnName;
            }
        }
        
        $this->attributes[ $type ] = $attributes;
        
        return  $this->attributes[ $type ];
    }
    
    function getLastModificationTime()
    {
        if( $this->lastModified ){
            return $this->lastModified;
        }
        
        $time = StructureDA::readTableCreateTime( $this->wc, $this->table );
        
        if( $time ){
            $this->lastModified  = new ExtendedDateTime($time);
        }
        
        return $this->lastModified;
    }
    
    static function create( WitchCase $wc, string $structureName )
    {
        foreach( Craft::TYPES as $type )
        {
            $table      = $type.'__'.$structureName;
            $className  = "WC\\Craft\\". ucfirst($type);
            
            if( StructureDA::createStructureTable( $wc, $table, $className::dbFields() ) === false ){
                return false;
            }
        }
        
        return true;
    }
    
    function update( $attributes )
    {
        $returnStatus = true;
        foreach( Craft::TYPES as $type )
        {
            $removeColumns = [];
            $changeColumns = [];
            foreach( array_keys($this->attributes( $type )) as $attributeName ){
                if( !isset($attributes[ $attributeName ]) ){
                    if( $type !== 'archive' ){
                        foreach( $this->attributes( $type )[ $attributeName ]['elements'] as $column ){
                            $removeColumns[] = $column;
                        }
                    }
                    else {
                        $attributeClass = $this->attributes( $type )[ $attributeName ]["class"];
                        $chgAttribute   = new $attributeClass(
                            $this->wc,
                            $attributeName,
                            $this->attributes( $type )[ $attributeName ]['parameters'] ?? []
                        );

                        foreach( $chgAttribute->dbFields as $key => $columnDef )
                        {
                            $columnName = $chgAttribute->tableColumns[ $key ];
                            $pos = strpos($columnDef, '@');
                            $changeColumns[ $columnName ] = substr($columnDef, 0, $pos).'__archive'. substr($columnDef, $pos);
                        }
                    }
                }
            }
            
            $addColumns = [];
            foreach( array_keys($attributes) as $attributeName ){
                if( !isset($this->attributes( $type )[ $attributeName ]) ){
                    foreach( $attributes[ $attributeName ]->dbFields as $column ){
                        $addColumns[] = $column;
                    }
                }
            }
                        
            $result = StructureDA::updateStructureTable( 
                $this->wc, 
                $type.'__'.$this->name, 
                $addColumns, 
                $removeColumns, 
                $changeColumns 
            );
            
            $returnStatus = $returnStatus && $result;
        }
        
        return $returnStatus;
    }
    
    function delete()
    {
        $tables = [];
        foreach( Craft::TYPES as $type ){
            $tables[] = $table = $type.'__'.$this->name;
        }
        
        $datas =  StructureDA::getWitchDataFromStructureTables($this->wc, $tables);
        
        $witchesByDepth = [];
        foreach( $datas as $witchData )
        {
            $witch = WitchHandler::instanciate($this->wc, $witchData);
            
            if( empty($witchesByDepth[ $witch->depth ]) ){
                $witchesByDepth[ $witch->depth ] = [];
            }
            
            $witchesByDepth[ $witch->depth ][ $witch->id ] = $witch;
        }
        
        foreach( array_reverse($witchesByDepth) as $witchesArray ){
            foreach( $witchesArray as $witch ){
                if( empty(WitchDA::fetchDescendants($this->wc, $witch->id, false)) ){
                    $witch->delete();
                }
                else {
                    $witch->edit([ 'craft_table' => null, 'craft_fk' => null ]);
                }
            }
        }
        
        $returnStatus = true;
        foreach( $tables as $table ){
            $returnStatus = $returnStatus && StructureDA::deleteStructureTable( $this->wc, $table );
        }
        
        return $returnStatus;
    }
    
    function createCraft( string $name=null, ?string $type=null, ?int $contentKey=null )
    {
        if( !$type || !in_array($type, Craft::TYPES) ){
            $craftTable =  $this->table;
        }
        else {
            $craftTable = $type.'__'.$this->name;
        }
        
        return StructureDA::createCraft($this->wc, $craftTable, $name, $contentKey);
    }
    
    static function listStructures( WitchCase $wc, bool $countElements=false )
    {
        $structures = StructureDA::listStructures($wc);
        
        if( $countElements ){
            foreach( $structures as $structureName => $structureData ){
                $structures[ $structureName ]['count'] = StructureDA::countElements( $wc, $structureData['name'] );
            }
        }
        
        return $structures;
    }
    
    /**
     * 
     * @param array $criterias 
     * @param bool $excludeCriterias default true
     * @return type
     */
    function searchBy( array $criterias, bool $excludeCriterias=true )
    {
        $craftedData    = WitchCrafting::craftQueryFromAttributeSearch( $this->wc, $this, $criterias, $excludeCriterias);
        
        $returnedCrafts = [];
        foreach( $craftedData ?? [] as $id => $data ){
            $returnedCrafts[ $id ] =  Craft::factory( $this->wc, $this, $data );
        }
        
        return $returnedCrafts;
    }
    
    
    function getFields( ?string $forcedType=null ): array
    {
        $type = $forcedType ?? $this->type;
        
        $fields = Craft::ELEMENTS;
        
        if( !in_array($type, Craft::TYPES) ){
            return $fields;
        }
        
        $className  = "WC\\Craft\\". ucfirst($type);
        
        array_push( $fields, ...($className::ELEMENTS ?? []) );
        
        return array_unique($fields);
    }
    
    function getJointure(): array
    {
        $craftTable        = trim( $this->wc->db->escape_string($this->table) );
        $jointuresParams    = [ 'craft_table' => $craftTable ];
        
        foreach( Craft::JOIN_TABLES as $joinTableData )
        {
            $table = $joinTableData['alias'] ?? $joinTableData['table'];
            $jointuresParams[ $table ] = $table.'|'.$craftTable;
        }

        $jointures          = [];
        foreach( Craft::JOIN_TABLES as $joinTableData )
        {
            $table = $joinTableData['alias'] ?? $joinTableData['table'];
            
            $jointureQueryPart  =   "LEFT JOIN ";
            $jointureQueryPart  .=  "`".$joinTableData['table']."` AS `".$jointuresParams[ $table ]."` ";
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
    
    function getJoinFields(): array
    {
        $craftTable        = trim( $this->wc->db->escape_string($this->table) );
        $jointuresParams    = [ 'craft_table' => $craftTable ];
        foreach( Craft::JOIN_TABLES as $joinTableData )
        {
            $table = $joinTableData['alias'] ?? $joinTableData['table'];
            $jointuresParams[ $table ] = $table.'|'.$craftTable;
        }
        
        $joinFields = [];
        foreach( Craft::JOIN_FIELDS as $joinField )
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

    
}
