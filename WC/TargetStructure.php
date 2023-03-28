<?php
namespace WC;

use WC\Targets\Content;
use WC\DataTypes\ExtendedDateTime;


class TargetStructure 
{
    var $table;
    var $type;
    var $name;
    var $created;
    var $attributes;
    
    var $exist;
    var $isArchive;
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc, $structureTableName )
    {
        $this->wc       = $wc;
        $this->table    = $structureTableName;
        
        $this->type     = substr( $this->table, 0, strpos($this->table, '_') );
        $this->name     = substr( $this->table, strpos($this->table, '_') + 1 );    
        
        $cache          = $this->wc->cache->get('system', $this->table);
        if( $cache ){
            include $cache;
        }
        
        if( empty($columns) )
        {
            $query      =   "SHOW COLUMNS FROM `".$this->table."` WHERE `Field` LIKE '%@%'";            
            $result     =   $this->wc->db->selectQuery($query);
            
            if( $result === false && $this->wc->db->errno() != 1146 ){
                $this->wc->log->error("Can't access to information_schema of: ".$this->table." in database", true);
            }
            
            $columns  = [];
            if( !$result  ){
                $this->exist = false;
            }
            else 
            {
                $this->exist = true;
                
                foreach( $result as $columnItem ){
                    $columns[ $columnItem["Field"] ] = $columnItem;
                }
                
                $this->wc->cache->create('system', $this->table, $columns, 'columns');
            }
        }
        
        $this->attributes   = [];
        foreach( array_keys($columns) as $columnName )
        {
            if( strcmp(substr($columnName, 0, 2), "@_") != 0 ){
                continue;
            }
            
            $attributeType  = substr( $columnName, 2, strpos($columnName, '__') - 2 );
            $attributeName  = substr( $columnName, strpos($columnName, '__') + 2 );
            
            $attributeElement = false;
            if( strstr($attributeType, "#") )
            {
                $buffer = explode("#", $attributeType);
                $attributeType      = $buffer[0];
                $attributeElement   = $buffer[1];
            }
            
            if( empty($this->attributes[ $attributeName ]) )
            {
                $className = "WC\\Attributes\\".ucfirst($attributeType).'Attribute';
                
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
        
        //$this->columns      = $columns;
        
        $this->created      = false;
        $this->isArchive    = false;
    }
    
    function createTime()
    {
        if( $this->created ){
            return $this->created;
        }
        
        $query  =   "SELECT table_name, create_time  ";
        $query  .=  "FROM information_schema.tables ";
        $query  .=  "WHERE table_type = 'BASE TABLE' ";
        $query  .=  "AND table_name LIKE 'archive_".$this->name."' ";
        
        $data           = $this->wc->db->fetchQuery($query);
        $this->created  = new ExtendedDateTime($data['create_time']);
        
        return $this->created;
    }
    
    function create()
    {
        $query  =   "CREATE TABLE `content_".$this->wc->db->escape_string($this->name)."` ( ";
        
        foreach( Target::$dbFields as $dbField ){
            $query  .=  $dbField.", ";
        }
        
        foreach( Content::$dbFields as $dbField ){
            $query  .=  $dbField.", ";
        }
        
        $query  .=  Target::$primaryDbField;
        
        $query  .=  ") ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 ";
        
        if( !$this->wc->db->createQuery($query) ){
            return false;
        }
        
        return true;
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
        
        $separator = "";
        $query = "";
        $query      .= "ALTER TABLE `content_".$this->wc->db->escape_string($this->name)."` ";
        foreach( $removeColumns as $column )
        {
            $query      .=  $separator."DROP `".$column."` ";
            $separator  =   ", ";
        }
        foreach( $addColumns as $column )
        {
            $query      .=  $separator."ADD ".$column." ";
            $separator  =   ", ";
        }
        
        $this->wc->cache->delete( 'system', $this->table );

        if( !$this->wc->db->alterQuery($query) ){
            return false;
        }
        
        
        
        return true;
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
                if( empty($witch->fetchDaughters()) ){
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
            
            if( $searchedAttributeName == $attributeName ){
                $queryWhereElements[]   = $attribute->searchCondition( $this->table, $search );
            }
            
            foreach( $attribute->tableColumns as $attributeElement => $attributeElementColumn )
            {
                $field  =   "`".$this->table."`.`".$attributeElementColumn."` ";
                $field  .=  "AS `".$this->table."|".$attributeName;
                $field  .=  "__".$attributeElement."` ";

                $querySelectElements[] = $field;
            }

            $leftJoinTableAliases = [];
            foreach( $attribute->joinTables as $joinTableData )
            {
                $leftJoinTableAlias         = $joinTableData['table'].'__'.$this->table.'__'.$attributeName;
                $leftJoinTableAliases[ '`'.$joinTableData['table'].'`' ] = '`'.$joinTableData['table'].'__'.$this->table.'__'.$attributeName.'`';

                $leftJoinTableCondition     = str_replace('%target_table%', $this->table, $joinTableData['condition']);
                $leftJoinTableCondition     = str_replace(array_keys($leftJoinTableAliases), array_values($leftJoinTableAliases), $leftJoinTableCondition);

                $queryTablesElements[ $this->table ][] = $joinTableData['table'].' AS '.$leftJoinTableAlias.' ON '.$leftJoinTableCondition;
            }

            foreach( $attribute->joinFields as $joinFieldItem )
            {
                $field = str_replace('%target_table%', $this->table, $joinFieldItem);
                $field = str_replace(array_keys($leftJoinTableAliases), array_values($leftJoinTableAliases), $field);

                $querySelectElements[] = $field;
            }
        }
        
        $result = [];
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
                $subBuffer      = explode('__', $buffer[1]);
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
