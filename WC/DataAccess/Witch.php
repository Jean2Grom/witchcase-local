<?php
namespace WC\DataAccess;

use WC\WitchCase;

class Witch
{
    static function readFromId( WitchCase $wc, int $id )
    {
        if( empty($id) ){
            return false;
        }

        $query = "";
        $query  .=  "SELECT * FROM witch ";
        $query  .=  "WHERE id = :id ";

        $data = $wc->db->fetchQuery($query, [ 'id' => $id ]);
        
        return $data;
    }
    
    static function update( WitchCase $wc, array $params, array $conditions )
    {
        if( empty($params) || empty($conditions) ){
            return false;
        }
        
        $query = "";
        $query  .=  "UPDATE `witch` ";
        
        $separator = "SET ";
        foreach( array_keys($params) as $field )
        {
            $query      .=  $separator.'`'.$wc->db->escape_string($field)."` = :".$field." ";
            $separator  =  ", ";
        }
        
        $separator = "WHERE ";
        foreach( array_keys($conditions) as $field )
        {
            $query      .=  $separator.'`'.$wc->db->escape_string($field)."` = :".$field." ";
            $separator  =  "AND ";
        }
        
        return $wc->db->updateQuery( $query, array_replace($params, $conditions) );
    }
    
    static function create( WitchCase $wc, array $params )
    {
        if( isset($params['id']) ){
            unset($params['id']);
        }
        if( isset($params['datetime']) ){
            unset($params['datetime']);
        }
        
        $query = "";
        $query  .=  "INSERT INTO `witch` ";
        
        $separator = "( ";
        foreach( array_keys($params) as $field )
        {
            $query  .=  $separator."`".$field."` ";
            $separator = ", ";
        }
        $query  .=  ") VALUES ";
        
        $separator = "( ";
        foreach( array_keys($params) as $field )
        {
            $query  .=  $separator.":".$field." ";
            $separator = ", ";
        }
        $query  .=  ") ";
        
        return $wc->db->insertQuery($query, $params);
    }
    
    static function increasePlateformDepth( WitchCase $wc )
    {
        $wc->cache->delete( 'system', 'depth' );
        $newLevelDepth = WitchSummoning::getDepth($wc) + 1;
        
        $query  =   "ALTER TABLE `witch` ";
        $query  .=  "ADD `level_".$newLevelDepth."` INT(11) UNSIGNED NULL DEFAULT NULL ";
        $query  .=  ", ADD KEY `IDX_level_".$newLevelDepth."` (`level_".$newLevelDepth."`) ";
        
        $wc->db->debugQuery($query);
        
        return $wc->db->alterQuery($query);        
    }
    
    static function getNewDaughterIndex( WitchCase $wc, array $position=[] )
    {
        $depth = count($position) + 1;
        
        $params = [];
        $query  = "SELECT MAX(`level_".$depth."`) AS `maxIndex` FROM `witch` ";
        
        $linkingCondition = "WHERE ";
        foreach($position as $level => $levelPosition )
        {
            $field              =   "level_".$level;
            $query              .=  $linkingCondition."`".$field."` = :".$field." ";
            $params[ $field ]   =   $levelPosition;
            $linkingCondition   =   "AND ";
        }
        
        $wc->db->debugQuery($query, $params);
        $result = $wc->db->fetchQuery($query, $params);
        
        if( !$result ){
            return false;
        }
        
        $max = (int) $result["maxIndex"];
        
        return $max + 1;
    }
    
    static function getUrlData(  WitchCase $wc, string $site, string $url, int $excludedId=null )
    {
        $params = [ 
            'site'      => $site,
            'url'       => $url,
            'regexp'    => '^'. $url.'-[0-9]+$',
        ];
        
        $query = "";
        $query  .=  "SELECT `url` ";
        $query  .=  "FROM `witch` ";
        $query  .=  "WHERE `site` = :site ";
        if( $excludedId )
        {
            $query  .=  "AND `id` <> :excludedId ";
            $params['excludedId'] = $excludedId;
        }
        $query  .=  "AND ( ";
        $query  .=      "`url` = :url ";
        $query  .=      "OR `url` REGEXP :regexp ";
        $query  .=  ") ";
        
        return $wc->db->selectQuery($query, $params);
    }
    
    function delete()
    {
        $query = "";
        $query  .=  "SELECT * ";
        $query  .=  "FROM `witch` ";
        
        $separator = "WHERE ";
        foreach( $this->position as $level => $coord )
        {
            $query  .=  $separator."`level_".$level."` = ".$coord." ";
            $separator = "AND ";
        }
        
        $result = $this->wc->db->selectQuery($query);
        
        $witchesToDeleteIds = [];
        foreach( $result as $row )
        {
            ( self::createFromData($this->wc, $row) )->deleteContent();
            $witchesToDeleteIds[] = $row['id'];
        }
        
        $query = "";
        $query  .=  "DELETE FROM `witch` ";
        $query  .=  "WHERE id IN ( ". implode(", ", $witchesToDeleteIds)." ) ";
        
        return $this->wc->db->deleteQuery($query);
    }
    
}
