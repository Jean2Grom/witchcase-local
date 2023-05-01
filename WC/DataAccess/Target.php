<?php
namespace WC\DataAccess;

use WC\WitchCase;

class Target 
{
    static function getRelatedTargetsIds( WitchCase $wc, string $table, int $id )
    {
        if( empty($table) || empty($id) ){
            return false;
        }
        
        $params = [ 'id' => $id, ];
        
        $query = "";
        $query  .=  "SELECT `id` ";
        $query  .=  "FROM `".$wc->db->escape_string($table)."` ";
        $query  .=  "WHERE `content_key` = :id ";
        
        $result =   $wc->db->selectQuery($query, $params);
        
        $ids = [];
        foreach( $result ?? [] as $row ){
            $ids[] = $row['id'];
        }
        
        return $ids;
    }
    
    static function countWitches( WitchCase $wc, string $table, int $id )
    {
        if( empty($table) || empty($id) ){
            return false;
        }
        
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
    
    static function getWitches( WitchCase $wc, string $table, int $id )
    {
        if( empty($table) || empty($id) ){
            return false;
        }
        
        $params = [
            'target_table'  => $table,
            'target_fk'     => $id,
        ];
        
        $query = "";
        $query  .=  "SELECT * ";
        $query  .=  "FROM `witch` ";
        $query  .=  "WHERE `witch`.`target_table` = :target_table ";
        $query  .=  "AND `witch`.`target_fk` = :target_fk ";
        
        return $wc->db->selectQuery($query, $params);
    }
        
    static function delete( WitchCase $wc, string $table, int $id )
    {
        if( empty($table) || empty($id) ){
            return false;
        }
        
        $cachedData = $wc->cache->read( WitchCrafting::CACHE_FOLDER, $table ) ?? [];
        if( isset($cachedData[ $id ]) ){
            $wc->cache->delete( WitchCrafting::CACHE_FOLDER, $table );
        }
        
        $query = "";
        $query  .=  "DELETE FROM `".$wc->db->escape_string($table)."` ";
        $query  .=  "WHERE `id` = :id ";
        
        return $wc->db->deleteQuery($query, [ 'id' => $id ]);
    }
    
    static function update( WitchCase $wc, string $table, array $fields, array $conditions )
    {
        if( empty($table) || empty($fields) || empty($conditions) ){
            return false;
        }

        $userId = $wc->user->id;
        if( $userId )
        {
            $fields["creator"]      = $fields["creator"] ?? $userId;
            $fields["modificator"]  = $fields["modificator"] ?? $userId;            
        }
        
        $params = []; 
        $query  = "";
        $query  .=  "UPDATE `".$wc->db->escape_string($table)."` ";
        
        $separator = "SET ";
        foreach( $fields as $field => $value )
        {
            $key            = md5($field.$value);
            $params[ $key ] = $value;
            $query  .=  $separator."`".$field."` = :".$key." ";
            $separator = ", ";
        }
        
        $separator = "WHERE ";
        foreach( $conditions as $field => $value )
        {
            $key            = md5($field.$value);
            $params[ $key ] = $value;
            
            $query  .=  $separator."`".$field."` = :".$key." ";
            $separator = "AND ";            
        }
        
        if( isset($conditions['id']) )
        {
            $cachedData = $wc->cache->read( WitchCrafting::CACHE_FOLDER, $table ) ?? [];
            if( isset($cachedData[ $conditions['id'] ]) ){
                $wc->cache->delete( WitchCrafting::CACHE_FOLDER, $table );
            }
        }
        
        return $wc->db->updateQuery($query, $params);
    }
    
}
