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
        $query = "";
        $query  .= "SELECT * ";
        $query  .= "FROM `witch` ";
        $query  .= "WHERE `target_table` LIKE \"content_".$this->wc->db->escape_string($this->name)."\" ";
        
        $datas = $this->wc->db->selectQuery($query);
        
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
        
        $this->wc->cache->delete( 'system', $this->table );
        
        $query = "DROP TABLE `content_".$this->wc->db->escape_string($this->name)."` ";
        if( !$this->wc->db->deleteQuery($query) ){
            return false;
        }
        
        return true;
    }
    
    function createTarget( $name=false )
    {
        $userId = $this->wc->user->id;
        if( !$userId ){
            $userId = 'NULL';
        }
        $query  =   "INSERT INTO `".$this->table."` ";
        $query  .=  "( `creator`";
        if( $name ){
            $query  .=  ", `name` ";
        }
        $query  .=  ", `modificator` ) ";
        $query  .=  "VALUES ( ".$userId." ";
        if( $name ){
            $query  .=  ', "'.$this->wc->db->escape_string($name).'" ';
        }
        $query  .=  ", ".$userId." ) ";
        
        return $this->wc->db->insertQuery($query);
        
    }
    
    static function listStructures( WitchCase $wc )
    {
        $query = "";
        $query  .=  "SELECT table_name AS tn ";
        $query  .=  ", create_time AS ct ";
        $query  .=  "FROM information_schema.tables ";
        $query  .=  "WHERE table_type = 'BASE TABLE' ";
        $query  .=  "AND table_name LIKE 'content_%' ";
        $query  .=  "ORDER BY table_name ASC ";
        
        $result =   $wc->db->multipleRowsQuery($query);
        
        $structures     = [];
        foreach( $result as $item )
        {
            $tableName  = $item['tn'];
            
            if( !str_starts_with($tableName, "content_") ){
                continue;
            } 
            
            $structureName = substr($tableName, strlen("content_"));
            
            $structures[ $structureName ] = [ 
                'name'      => $structureName, 
                'table'     => $tableName, 
                'created'   => $item['ct'],
            ];
        }
        
        return $structures;
    }
    
    static function count( WitchCase $wc, $archives=false )
    {
        $query  =   "SELECT count(*)  ";
        $query  .=  "FROM information_schema.tables ";
        $query  .=  "WHERE table_type = 'BASE TABLE' ";
        
        if( !$archives ){
            $query  .=  "AND table_name LIKE 'content_%' ";
        }
        else {
            $query  .=  "AND table_name LIKE 'archive_%' ";
        }
        
        $data = $wc->db->fetchQuery($query);
        
        return $data['count(*)'];
    }
    
    static function countElements( WitchCase $wc, $structure )
    {
        $count = [];
        foreach( ['draft', 'content', 'archive'] as $type ) 
        {
            $query  =   "SELECT COUNT(*) ";
            $query  .=  "FROM `".$type."_".$structure."` ";
            
            $countData  = $wc->db->fetchQuery($query);
            
            if( $countData !== false ){
                $count[$type] = $countData['COUNT(*)'];
            }
            else {
                $count[$type] = "Not available";
            }
        }
        
        return $count;
    }
    
    function searchBy( $searchedAttributeName, $search )
    {
        
        $querySelectElements    = [];
        $queryTablesElements    = [];
        $queryWhereElements     = [];
        
        $queryTablesElements[ $this->table ] = [];
        foreach( array_keys(Target::ELEMENTS) as $commonStructureField )
        {
            $field  =   "`".$this->table."`.`".$commonStructureField."` ";
            $field  .=  "AS `".$this->table."|".$commonStructureField."` ";
            $querySelectElements[] = $field;
        }

        foreach( $this->attributes as $attributeName => $attributeData )
        {
            $attribute = new $attributeData['class']( $this->wc, $attributeName );
            
            array_push( $querySelectElements, ...$attribute->getSelectFields($this->table) );
            $queryTablesElements[ $this->table ] = $attribute->getJointure( $this->table );
            
            if( $searchedAttributeName == $attributeName ){
                $queryWhereElements[]   = $attribute->searchCondition( $this->table, $search );
            }
        }
        
        $query = "";
        $query  .=  "SELECT ".implode( ', ', $querySelectElements)." ";
        $separator = "FROM ";
        foreach( $queryTablesElements as $fromTable => $leftJoinArray )
        {
            $query  .=  $separator." `".$fromTable."` ";
            $separator = ", ";
            
            foreach( $leftJoinArray as $leftJoin ){
                $query  .=  "LEFT JOIN ".$leftJoin." ";
            }
        }
        
        $query  .=  "WHERE ".implode( 'AND ', $queryWhereElements)." ";

        $result = $this->wc->db->selectQuery($query);
        
        $craftedData = [];
        foreach( $result as $row ){
            foreach( $row as $rowField => $rowFieldValue )
            {
                $buffer         = explode('|', $rowField);
                $table          = $buffer[0];
                $subBuffer      = explode('#', $buffer[1]);
                $field          = $subBuffer[0];
                $fieldElement   = $subBuffer[1] ?? false;
                $currentId      = $row[ $table.'|id' ];

                if( empty($craftedData[ $table ]) ){
                    $craftedData[ $table ] = [];
                }
                if( empty($craftedData[ $table ][ $currentId ]) ){
                    $craftedData[ $table ][ $currentId ] = [];
                }
                if( empty($craftedData[ $table ][ $currentId ][ $field ]) ){
                    $craftedData[ $table ][ $currentId ][ $field ] = [];
                }
                
                if( empty($fieldElement) ){
                    $craftedData[ $table ][ $currentId ][ $field ] = $rowFieldValue;
                }
                elseif( empty($craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ]) ){
                    $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ] = $rowFieldValue;
                }
                elseif( !is_array($craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ]) )
                {
                    $prevValue = $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ];

                    if( $prevValue != $rowFieldValue ){
                        $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ] = [
                            $prevValue,
                            $rowFieldValue,
                        ];
                    }
                }
                //elseif( !in_array($rowFieldValue, $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ]) ){
                else {
                    $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ][] = $rowFieldValue;
                }
            }
        }
        
        $structureTargetData = $craftedData[ $this->table ] ?? [];
        
        $returnedTargets = [];
        foreach( $structureTargetData as $targetId => $targetCraftedData ){
            $returnedTargets[ $targetId ] = new Target( $this->wc, $this, $targetCraftedData );
        }
        
        return $returnedTargets;
        
    }
}
