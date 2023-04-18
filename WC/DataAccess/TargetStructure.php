<?php
namespace WC\DataAccess;

use WC\WitchCase;

class TargetStructure 
{
    const CACHE_FOLDER = "structure";
    
    static function readStructure( WitchCase $wc, string $table ) 
    {
        $columns = $wc->cache->read( self::CACHE_FOLDER, $table );
        
        if( empty($columns) )
        {
            $query  =   "SHOW COLUMNS FROM `".$wc->db->escape_string($table)."` WHERE `Field` LIKE '%@%' ";
            $wc->db->debugQuery($query);
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
}
