<?php
namespace WC;

use WC\DataAccess\TargetStructure as TargetStructureDA;

use WC\Target\Content;
use WC\Datatype\ExtendedDateTime;
use WC\Attribute;



class TargetStructure 
{
    var $table;
    var $type;
    var $name;
    var $attributes;
    var $fields;
    
    var $lastModified;
    var $exist;
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc, string $structureTableName )
    {
        $this->wc       = $wc;
        $this->table    = $structureTableName;        
        $this->type     = substr( $this->table, 0, strpos($this->table, '_') );
        $this->name     = substr( $this->table, strpos($this->table, '_') + 1 );    
        $columns        = TargetStructureDA::readTableStructure($this->wc, $this->table);
        
        $this->fields       = [];
        $this->attributes   = [];
        foreach( array_keys($columns) as $columnName )
        {
            if( strpos($columnName, '@') === false )
            {
                $this->fields[] = $columnName;
                continue;
            }
            
            $splitColumn = Attribute::splitColumn($columnName);
            
            $attributeName      = $splitColumn['name'];
            $attributeType      = $splitColumn['type'];            
            $attributeElement   = $splitColumn['element'];
            
            if( !$attributeType ){
                continue;
            }
            
            if( empty($this->attributes[ $attributeName ]) )
            {
                $className = "WC\\Attribute\\".ucfirst($attributeType).'Attribute';
                
                if( !class_exists($className) )
                {
                    $this->wc->debug( "Attribute ".$attributeName." : class not found \"".$className."\", skip" );
                    continue;
                }
                
                $this->attributes[ $attributeName ] = [
                    'class'     => $className,
                    'elements'  => [],
                ];
            }
            
            if( empty($attributeElement) ){
                $this->attributes[ $attributeName ]['elements'][ $attributeType ]       = $columnName;
            }
            else {
                $this->attributes[ $attributeName ]['elements'][ $attributeElement ]    = $columnName;
            }
        }
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
        $table      = "content_".$structureName;
        $dbFields   = array_merge( Target::$dbFields, Content::$dbFields, [Target::$primaryDbField] );
        
        return TargetStructureDA::createTargetStructureTable( $wc, $table, $dbFields );
    }
    
    function update( $attributes )
    {
        $removeColumns = [];
        foreach( array_keys($this->attributes) as $attributeName ){
            if( !isset($attributes[ $attributeName ]) ){
                foreach( $this->attributes[ $attributeName ]['elements'] as $column ){
                    $removeColumns[] = $column;
                }
            }
        }
        
        $addColumns = [];
        foreach( array_keys($attributes) as $attributeName ){
            if( !isset($this->attributes[ $attributeName ]) ){
                foreach( $attributes[ $attributeName ]->dbFields as $column ){
                    $addColumns[] = $column;
                }
            }
        }
        
        return TargetStructureDA::updateTargetStructureTable( $this->wc, $this->table, $addColumns, $removeColumns );
    }
    
    function delete()
    {
        $datas =  TargetStructureDA::getWitchDataFromTargetStructureTables($this->wc, [ $this->table ]);
        
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
        
        return TargetStructureDA::deleteTargetStructureTable( $this->wc, $this->table );
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
    
}
