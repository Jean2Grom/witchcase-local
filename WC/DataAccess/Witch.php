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
    
    static function urlCleanupString( $urlRaw )
    {
        $url    = "";
        $buffer = explode('/', $urlRaw);
        foreach( $buffer as $bufferElement )
        {
            $urlPart = self::cleanupString( $bufferElement );
            if( !empty($bufferElement) ){
                $url .= "/".$urlPart;
            }
        }
        
        if( empty($url) ){
            $url = '/';
        }
        
        return $url;
    }


    static function cleanupString( $string )
    {
        $characters =   array(
                'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 
                'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
                'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 
                'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
                'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 
                'í' => 'i', 'î' => 'i', 'ï' => 'i',
                'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 
                'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
                'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 
                'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
                'Œ' => 'oe', 'œ' => 'oe',
                '$' => 's'  );
        
        $string = strtr($string, $characters);
        $string = preg_replace('#[^A-Za-z0-9]+#', '-', $string);
        $string = trim($string, '-');
        $string = strtolower($string);
        
        return $string;
    }
    
    
    function createDaughter( $params )
    {
        $name = $params['name'] ?? "";
        $name = trim($name);
        if( empty($name) ){
            return false;
        }
        
        if( $this->depth == $this->wc->website->depth ){
            $this->addLevel();
        }
        
        $site   = $params['site'] ?? "";
        $site   = trim($site);
        $url    = $params['url'] ?? "";
        $url    = trim($url);
        if( !empty($site) && empty($url) )
        {
            if( $this->site == $site ){
                $url    =   $this->url;
            }
            else {
                $url    =   $this->findPreviousUrlForSite( $site );
            }
            
            if( empty($url) ){
                $url = "/";
            }
            else
            {
                if( substr($url, -1) != '/' ){
                    $url .= '/';
                }
                $url    .=  self::cleanupString($name);
            }
        }
        elseif( !empty($site) && !empty($url) ){
            $url    =   self::urlCleanupString($url);
        }
        
        if( !empty($url) ){
            $url = $this->checkUrl( $site, $url );
        }
        
        $newDaughterPosition                        = $this->position;
        $newDaughterPosition[ ($this->depth + 1) ]  = $this->getNewDaughterIndex();
        
        $excludedAutofields = [
            'id',
            'name',
            'site',
            'url',
            'datetime',
        ];
        
        $query  =   "INSERT INTO `witch` ";
        
        $query  .=  "( `name`, `site`, `url` ";
        $fields = [];
        foreach( self::FIELDS as $fieldItem )
        {
            if( in_array($fieldItem, $excludedAutofields) || !in_array($fieldItem, array_keys( $params )) ){
                continue;
            }
            $query  .=  ", `".$fieldItem."` ";
            
            $fields[] = $fieldItem;
        }
        foreach( $newDaughterPosition as $level => $levelPosition ){
            $query  .=  ", `level_".$level."` "; 
        }
        $query  .=  " ) ";
        
        $query  .=  "VALUES ( '".$this->wc->db->escape_string($name)."' ";
        //$query  .=  ", '".$this->wc->db->escape_string($site)."' ";
        $query  .=  ", ".(empty($site)? "NULL": "'".$this->wc->db->escape_string($site)."'")." ";
        //$query  .=  ", '".$url."' ";
        $query  .=  ", ".(empty($url)? "NULL": "'".$url."'")." ";
        foreach( $fields as $fieldItem ){
            $query  .=  ", '".$this->wc->db->escape_string($params[ $fieldItem ])."' ";
        }
        foreach( $newDaughterPosition as $level => $levelPosition ){
            $query  .=  ", '".$levelPosition."'";
        }
        $query  .=  " ) ";
        
        return $this->wc->db->insertQuery($query);
    }
    
    function addLevel()
    {
        $newLevelDepth = $this->depth + 1;
        
        $query  =   "ALTER TABLE `witch` ";
        $query  .=  "ADD `level_".$newLevelDepth."` INT(11) UNSIGNED NULL DEFAULT NULL ";
        $query  .=  ", ADD KEY `IDX_level_".$newLevelDepth."` (`level_".$newLevelDepth."`) ";

        $result = $this->wc->db->alterQuery($query);
        
        if( !$result ){
            return false;
        }
        
        $this->wc->website->depth = $newLevelDepth;
        
        $this->wc->cache->delete( 'system', 'depth' );
        
        return true;
    }
    
    private function getNewDaughterIndex()
    {
        $depth = $this->depth + 1;
        
        $query  =   "SELECT MAX(`level_".$depth."`) AS maxIndex FROM `witch` ";
        
        $linkingCondition = "WHERE ";
        foreach($this->position as $level => $levelPosition )
        {
            $query  .=  $linkingCondition."`level_".$level."` = '".$levelPosition."' ";
            $linkingCondition = "AND ";
        }
        
        $result = $this->wc->db->fetchQuery($query);
        
        if( !$result ){
            return false;
        }
        
        $max = (int) $result["maxIndex"];
        
        return $max + 1;
    }
    
    function checkUrl( $site, $url )
    {
        $regexp = '^'. $url.'-[0-9]+$';
        
        $query = "";
        $query  .=  "SELECT url ";
        $query  .=  "FROM `witch` ";
        $query  .=  "WHERE site = '".$this->wc->db->escape_string($site)."' ";
        $query  .=  "AND id <> ".$this->id." ";
        $query  .=  "AND ( ";
        $query  .=      "url = '".$url."' ";
        $query  .=      "OR url REGEXP '".$regexp."' ";
        $query  .=  ") ";
        
        $result = $this->wc->db->selectQuery($query);
        
        if( empty($result) ){
            return $url;
        }
        
        $regex = '/^'. str_replace('/', '\/', $url).'(?:-\d+)?$/';
        
        $lastIndice = 0;
        foreach( $result as $row )
        {
            $match = [];
            preg_match($regex, $row['url'], $match);
            
            if( !empty($match) )
            {
                $indice = substr($row['url'], (1 + strrpos($row['url'], '-') ) );
                
                if( $indice > $lastIndice )
                {
                    $lastIndice = $indice;
                    $url        = substr($row['url'], 0, strrpos($row['url'], '-') ).'-'.($indice + 1);
                }
            }
        }
        
        if( $lastIndice == 0 ){
            $url .= '-2';
        }
        
        return $url;
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
    
    function deleteContent()
    {
        if( !$this->hasTarget() ){
            return false;
        }
        
        $targetTable    = $this->target_table;
        $targetId       = $this->target_fk;
        
        $query = "";
        $query  .=  "SELECT count(`id`) AS qtt ";
        $query  .=  "FROM `witch` ";
        $query  .=  "WHERE `witch`.`target_table` = '".$targetTable."' ";
        $query  .=  "AND `witch`.`target_fk` = ".$targetId." ";
        
        $result = $this->wc->db->fetchQuery($query);
        
        $singleContent = ($result['qtt'] == 1);
        
        if( !$this->edit(['target_table' => 'NULL', 'target_fk' => 'NULL']) ){
            return false;
        }
        
        $this->target = NULL;
        
        if( $singleContent )
        {
            $query = "";
            $query  .=  "DELETE FROM `".$targetTable."` ";
            $query  .=  "WHERE `id` = ".$targetId." ";
            
            return $this->wc->db->deleteQuery($query);
        }
        
        return false;
    }
    
}
