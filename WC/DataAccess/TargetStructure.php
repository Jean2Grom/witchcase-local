<?php
namespace WC\DataAccess;

use WC\WitchCase;

class TargetStructure 
{
    static function readStructure( WitchCase $wc, string $table ) 
    {
        $cache          = $wc->cache->get( 'system', $table );
        
        if( $cache ){
            include $cache;
        }
        
        if( empty($columns) )
        {
            $query      =   "SHOW COLUMNS FROM `".$wc->db->escape_string($table)."` WHERE `Field` LIKE '%@%' ";

            $result = $wc->db->selectQuery($query);
            
            if( !$result ){
                return false;
            }
            
            $columns  = [];
            foreach( $result as $columnItem ){
                $columns[ $columnItem["Field"] ] = $columnItem;
            }
            
            $wc->cache->create('system', $table, $columns, 'columns');            
        }
        
        return $columns;
    }
}
