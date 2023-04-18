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
}
