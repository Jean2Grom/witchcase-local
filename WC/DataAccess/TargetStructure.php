<?php
namespace WC\DataAccess;

use WC\WitchCase;

class TargetStructure 
{
    const CACHE_FOLDER = "structure";
    
    static function readTableStructure( WitchCase $wc, string $table ) 
    {
        if( empty($table) ){
            return false;
        }
        
        $columns = $wc->cache->read( self::CACHE_FOLDER, $table );
        
        if( is_null($columns) )
        {
            $query  =   "SHOW COLUMNS FROM `".$wc->db->escape_string($table)."` ";
            
            $result = $wc->db->selectQuery($query);
            
            if( $result === false ){
                return false;
            }
            
            $columns  = [];
            foreach( $result as $columnItem ){
                $columns[ $columnItem["Field"] ] = $columnItem;
            }
            
            $wc->cache->create( self::CACHE_FOLDER, $table, $columns );
        }
        
        return $columns;
    }
    
    static function readTableCreateTime( WitchCase $wc, string $table )
    {
        if( empty($table) ){
            return false;
        }
        
        $query = "";
        $query  .=  "SELECT `CREATE_TIME` AS `time` ";
        $query  .=  "FROM `information_schema`.`tables` ";
        $query  .=  "WHERE `table_type` = 'BASE TABLE' ";
        $query  .=  "AND table_name LIKE :table ";
        
        $result = $wc->db->fetchQuery($query, [ 'table' => $table ]);
        
        return $result['time'] ?? false;
    }
    
    static function createTargetStructureTable( WitchCase $wc, string $table, array $columns )
    { 
        if( empty($table) || empty($columns) ){
            return false;
        }
        
        $query = "";
        $query  .=  "CREATE TABLE `".$wc->db->escape_string($table)."` ( ";
        $query  .=  implode( ", ", $columns );
        $query  .=  ") ";
        
        return $wc->db->createQuery($query);
    }
    
    static function updateTargetStructureTable( WitchCase $wc, string $table, array $addColumns=[], array $removeColumns=[] )
    { 
        if( empty($table) || (empty( $addColumns ) && empty( $removeColumns )) ){
            return false;
        }
        
        $query = "";
        $query  .=  "ALTER TABLE `".$wc->db->escape_string($table)."` ";
        
        $separator = "";
        foreach( $removeColumns as $column )
        {
            $query      .=  $separator." DROP `".$column."` ";
            $separator  =   ", ";
        }
        foreach( $addColumns as $column )
        {
            $query      .=  $separator." ADD ".$column." ";
            $separator  =   ", ";
        }
        
        $wc->db->alterQuery($query);
        $wc->cache->delete( self::CACHE_FOLDER, $table );
        
        return true;
    }
    
    static function getWitchDataFromTargetStructureTables( WitchCase $wc, array $tables )
    {
        if( empty($tables) ){
            return false;
        }
        
        $params = [];        
        $query = "";
        $query  .= "SELECT * ";
        $query  .= "FROM `witch` ";
        $query  .= "WHERE ";
        
        $separator = "";
        foreach( $tables as $i => $tableName )
        {
            $key = 'table'.$i;
            $query  .=  $separator."`target_table` LIKE :".$key." ";
            $params[ $key ] = $tableName;
            $separator      = "OR ";
        }
        
        return $wc->db->selectQuery($query, $params);        
    }
    
    
    static function deleteTargetStructureTable( WitchCase $wc, string $table )
    { 
        if( empty($table) ){
            return false;
        }
    
        $query = "DROP TABLE `".$wc->db->escape_string($table)."` ";
        
        $wc->db->deleteQuery($query);
        
        $wc->cache->delete( self::CACHE_FOLDER, $table );
        
        return true;
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
    
    static function countElements( WitchCase $wc, string $structure )
    {
        $typesArray = [
            //'draft', 
            'content', 
            //'archive',
        ];
        
        $count = [];
        foreach( $typesArray as $type ) 
        {
            $query  =   "SELECT COUNT(*) ";
            $query  .=  "FROM `".$type."_".$structure."` ";
            
            $count[$type]  = $wc->db->countQuery($query);
        }
        
        return $count;
    }
    
    static function createTarget( WitchCase $wc, string $table, string $name=null )
    {
        $userId = $wc->user->id;
        $params = [];
        if( $userId )
        {
            $params["creator"]      = $userId;
            $params["modificator"]  = $userId;            
        }
        if( $name ){
            $params["name"]         = $name;
        }
        
        $query = "";
        $query  .=  "INSERT INTO `".$wc->db->escape_string($table)."` ( ";
        if( !empty($params) ){
            $query  .=  "`".implode( "`, `", array_keys($params) )."`";
        }
        $query  .=  ") VALUES ( ";
        if( !empty($params) ){
            $query  .=  ":".implode( ", :", array_keys($params) )." ";
        }
        $query  .=  ") ";
        
        return $wc->db->insertQuery($query, $params);
    }    
}
