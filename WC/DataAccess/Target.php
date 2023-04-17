<?php
namespace WC\DataAccess;

use WC\WitchCase;

class Target 
{
    static function countWitches( WitchCase $wc, string $table, int $id )
    {
        $params = [
            'target_table'  => $table,
            'target_fk'     => $id,
        ];
        
        $query = "";
        $query  .=  "SELECT count(`id`) ";
        $query  .=  "FROM `witch` ";
        $query  .=  "WHERE `witch`.`target_table` = :target_table ";
        $query  .=  "AND `witch`.`target_fk` = :target_fk ";
        
        return $wc->db->countQuery($query, $params);
    }
    
    static function delete( WitchCase $wc, string $table, int $id )
    {
        $cachedData = $wc->cache->read( 'craft', $table ) ?? [];
        if( isset($cachedData[ $id ]) ){
            unset($cachedData[ $id ]);
        }
        
        $wc->cache->create( 'craft', $table, $cachedData );
                
        $query = "";
        $query  .=  "DELETE FROM `".$wc->db->escape_string($table)."` ";
        $query  .=  "WHERE `id` = :id ";
        
        return $wc->db->deleteQuery($query, [ 'id' => $id ]);
    }
}
