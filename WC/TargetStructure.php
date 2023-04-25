<?php
namespace WC;

use WC\DataAccess\TargetStructure as TargetStructureDA;
use WC\DataAccess\Witch as WitchDA;
use WC\DataAccess\WitchCrafting;

use WC\Datatype\ExtendedDateTime;
use WC\Attribute;

class TargetStructure 
{
    var $table;
    var $type;
    var $name;
    var $attributes;    
    var $lastModified;
    var $exist;
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc, string $structureOrTableName, ?string $forcedType=null )
    {
        $this->wc = $wc;
        
        foreach( Target::TYPES as $type ){
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
            $this->type     = Target::TYPES[0];
            $this->table    = $this->type.'__'.$this->name;
        }
        
        if( $forcedType && in_array($forcedType, Target::TYPES) )
        {
            $this->type     = $forcedType;
            $this->table    = $this->type.'__'.$this->name;
        }
    }
                
    function attributes( string $requiredType=null, bool $forceReading=false )
    {
        $type = $requiredType ?? $this->type;
        
        if( isset($this->attributes[ $type ]) && !$forceReading ){
            return  $this->attributes[ $type ];
        }
        
        $table      = $type.'__'.$this->name;        
        $columns    = TargetStructureDA::readTableStructure($this->wc, $table);
        
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
        
        $time = TargetStructureDA::readTableCreateTime( $this->wc, $this->table );
        
        if( $time ){
            $this->lastModified  = new ExtendedDateTime($time);
        }
        
        return $this->lastModified;
    }
    
    static function create( WitchCase $wc, string $structureName )
    {
        foreach( Target::TYPES as $type )
        {
            $table      = $type.'__'.$structureName;
            $className  = "WC\\Target\\". ucfirst($type);
            $dbFields   = array_merge( Target::$dbFields, $className::$dbFields, [Target::$primaryDbField] );
            
            if( TargetStructureDA::createTargetStructureTable( $wc, $table, $dbFields ) === false ){
                return false;
            }
        }
        
        return true;
    }
    
    function update( $attributes )
    {
        $returnStatus = true;
        foreach( Target::TYPES as $type )
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
                        
            $result = TargetStructureDA::updateTargetStructureTable( 
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
        foreach( Target::TYPES as $type ){
            $tables[] = $table = $type.'__'.$this->name;
        }
        
        $datas =  TargetStructureDA::getWitchDataFromTargetStructureTables($this->wc, $tables);
        
        $witchesByDepth = [];
        foreach( $datas as $witchData )
        {
            $witch = Witch::createFromData($this->wc, $witchData);
            
            if( empty($witchesByDepth[ $witch->depth ]) ){
                $witchesByDepth[ $witch->depth ] = [];
            }
            
            $witchesByDepth[ $witch->depth ][ $witch->id ] = $witch;
        }
        
        foreach( array_reverse($witchesByDepth) as $witchesArray ){
            foreach( $witchesArray as $witch ){
                if( empty(WitchDA::fetchDescendants($this->wc, $witch->id, false, false)) ){
                    $witch->delete();
                }
                else {
                    $witch->edit([ 'target_table' => 'NULL', 'target_fk' => 'NULL' ]);
                }
            }
        }
        
        $returnStatus = true;
        foreach( $tables as $table ){
            $returnStatus = $returnStatus && TargetStructureDA::deleteTargetStructureTable( $this->wc, $table );
        }
        
        return $returnStatus;
    }
    
    function createTarget( string $name=null ){
        return TargetStructureDA::createTarget($this->wc, $this->table, $name);
    }
    
    static function listStructures( WitchCase $wc, bool $countElements=false )
    {
        $structures = TargetStructureDA::listStructures($wc);
        
        if( $countElements ){
            foreach( $structures as $structureName => $structureData ){
                $structures[ $structureName ]['count'] = TargetStructureDA::countElements( $wc, $structureData['name'] );
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
        
        $returnedTargets = [];
        foreach( $craftedData ?? [] as $targetId => $targetCraftedData ){
            $returnedTargets[ $targetId ] =  Target::factory( $this->wc, $this, $targetCraftedData );
        }
        
        return $returnedTargets;
    }
    
}
