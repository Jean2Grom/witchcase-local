<?php
namespace WC\DataAccess;

use WC\WitchCase;
use WC\Craft\Draft;
use WC\Craft\Archive;

class Craft 
{
    static function getRelatedCraftsIds( WitchCase $wc, string $table, int $id )
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
    
    static function countWitches( WitchCase $wc, ?string $table, ?int $id )
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
        
    static function getWitchesFromContentKey( WitchCase $wc, string $table, int $contentKey )
    {
        if( empty($table) || empty($contentKey) ){
            return false;
        }
        
        $params = [
            'target_table'  => $table,
            'content_key'   => $contentKey,
        ];
        
        $tableSql = $wc->db->escape_string($table);
        
        $query = "";
        $query  .=  "SELECT `witch`.* ";
        $query  .=  "FROM `".$tableSql."` ";
        $query  .=  "LEFT JOIN `witch` ";
        $query  .=      "ON `witch`.`target_fk` = `".$tableSql."`.`id` ";
        $query  .=      "AND `witch`.`target_table` = :target_table ";
        $query  .=  "WHERE `".$tableSql."`.`content_key` = :content_key ";
        $query  .=  "AND `witch`.`id` IS NOT NULL ";
        
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
    
    static function cleanupContentKey( WitchCase $wc, string $structureName, int $contentKey )
    {
        if( empty($structureName) || empty($contentKey) ){
            return false;
        }
        
        $draftIds   = self::getRelatedCraftsIds($wc, Draft::TYPE.'__'.$structureName, $contentKey);
        $archiveIds = self::getRelatedCraftsIds($wc, Archive::TYPE.'__'.$structureName, $contentKey);
        
        if( empty($draftIds) && empty($archiveIds) ){
            return;
        }
        
        $query = "";
        $query  .=  "SELECT count(`id`) ";
        $query  .=  "FROM `witch` ";        
        $query  .=  "WHERE ";
        
        $params = [];
        if( !empty($draftIds) )
        {
            $params['draft_target_table'] = Archive::TYPE.'__'.$structureName;
            
            foreach( $draftIds as $i => $id ){
                $params[ 'draft_id_'.$i ] = $id;
            }
            
            $query  .=  "( `witch`.`target_table` = :draft_target_table ";
            $query  .=      "AND `witch`.`target_fk` IN ( :draft_id_".implode(', :draft_id_', array_keys($draftIds))." ) ";
            $query  .=  ") ";
        }
        
        if( !empty($draftIds) && !empty($archiveIds) ){
             $query  .=  "OR ";
        }
        
        if( !empty($archiveIds) )
        {
            $params['archive_target_table'] = Draft::TYPE.'__'.$structureName;
            
            foreach( $archiveIds as $i => $id ){
                $params[ 'archive_id_'.$i ] = $id;
            }
            
            $query  .=  "( `witch`.`target_table` = :archive_target_table ";
            $query  .=      "AND `witch`.`target_fk` IN ( :archive_id_".implode(', :draft_id_', array_keys($archiveIds))." ) ";
            $query  .=  ") ";
        }
        
        $count = $wc->db->countQuery($query, $params);
        
        if( $count === 0 )
        {
            $query = "";
            $query  .=  "DELETE FROM `".$wc->db->escape_string( Draft::TYPE.'__'.$structureName )."` ";
            $query  .=  "WHERE `content_key` = :content_key ";

            $wc->db->deleteQuery($query, [ 'content_key' => $contentKey ]);
            
            $query = "";
            $query  .=  "DELETE FROM `".$wc->db->escape_string( Archive::TYPE.'__'.$structureName )."` ";
            $query  .=  "WHERE `content_key` = :content_key ";

            $wc->db->deleteQuery($query, [ 'content_key' => $contentKey ]);            
        }
        
        return;
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
