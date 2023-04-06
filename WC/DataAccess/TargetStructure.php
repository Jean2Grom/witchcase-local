<?php
namespace WC\DataAccess;

use WC\WitchCase;

class TargetStructure 
{
    const CACHE_FOLDER = "structure";
    
    static function readStructure( WitchCase $wc, string $table ) 
    {
        $columns    = null;    
        $cache      = $wc->cache->get( self::CACHE_FOLDER, $table );
        
        if( $cache ){
            include $cache;
        }
        
        if( empty($columns) )
        {
            $query  =   "SHOW COLUMNS FROM `".$wc->db->escape_string($table)."` WHERE `Field` LIKE '%@%' ";
            $result = $wc->db->selectQuery($query);
            
            if( !$result ){
                return false;
            }
            
            $columns  = [];
            foreach( $result as $columnItem ){
                $columns[ $columnItem["Field"] ] = $columnItem;
            }
            
            $wc->cache->create( self::CACHE_FOLDER, $table, $columns, 'columns' );
        }
        
        return $columns;
    }
}
