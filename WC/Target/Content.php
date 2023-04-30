<?php
namespace WC\Target;

use WC\WitchCase;
use WC\Target;

class Content extends Target 
{
    const TYPE          = 'content';
    
    static $dbFields    =   [];
    
    function set( $args )
    {
        return $this->setTarget( $args, self::$datatypes );
    }
    
    
    function countActiveDrafts()
    {
        $draftTable = "draft_".$this->structure;
        
        $query  =   "SELECT COUNT(*) FROM `".$this->wc->db->escape_string($draftTable)."` ";
        $query  .=  "WHERE `content_key` = '".$this->id."' ";
        $query  .=  "AND `content_key` = '".$this->id."' ";
        $query  .=  "AND `creation_date` > '".$this->modification_date->format('Y-m-d H:i:s')."' ";
        
        $draftCount = $this->wc->db->countQuery($query);
        
        return $draftCount;
    }
    
    function delete( bool $deleteAttributes=true )
    {
        $this->archive();
        
        
        if( !$archiveID )
        {   return false;   }
        
//        if( !Localisation::changeTarget( $this->table, $this->id, $archiveTable, $archiveID ) )
//        {   return false;   }
        
        $draftTable = "draft_".$this->structure;
        
        $query  =   "SELECT * FROM `".$this->wc->db->escape_string($draftTable)."` ";
        $query  .=  "WHERE content_key = '".$this->wc->db->escape_string($this->id)."'";
        
        $draftsData = $this->wc->db->multipleRowsQuery($query);
        
        if( $draftsData === false )
        {
            $this->wc->log->error("Search Drafts query failed: \"".$query."\" possible corrupted Database !");
            return false;
        }
        
        foreach( $draftsData as $draftsDataItem)
        {
            $draft  = new self( $this->structure );
            $draft->set($draftsDataItem);
            
            if( !$draft->delete() )
            {
                $message    = "Cannot delete and archive draft ".$draft->name;
                $message   .= ", aborting publication ";
                $this->wc->log->error($message);
                
                return false;
            }
        }
        
        $query  =   "DELETE FROM `".$this->table."` ";
        $query  .=  "WHERE id='".$this->id."' ";
        
        return $this->wc->db->deleteQuery( $query );
    }
    
    function archive()
    {
        $userID         = $this->wc->user->id;
        $currentDate    = date("Y-m-d H:i:s");
        
        $query  =   "INSERT INTO `archive_".$this->wc->db->escape_string($this->structure)."` ";
        $query  .=  "(`content_key`, `name`, `context`, `last_modificator`, ";
        $query  .=  "`last_modification_date`, `archiver`, `archive_date`";
        
        $orderAttributesKeys = [];
        foreach( $this->attributes as $attributeName => $attribute )
        {
            $orderAttributesKeys[ $attributeName ] = [];
            foreach($attribute->tableColumns as $key => $tableColumn)
            {
                $orderAttributesKeys[ $attribute->name ][] = $key;  
                $query .= ", `".$tableColumn."`";
            }
        }
        
        $query  .=  " ) ";
        
        if( is_array($this->context) )
        {
            $buffer = array();
            foreach( $this->context as $label => $value ){
                $buffer[] = $label.":".$value;
            }
            
            $contextString = implode( ",", $buffer );
        }
        else {
            $contextString = $this->context;
        }
        
        $query  .=  "VALUES ( '".$this->id."', ";
        $query  .=  "'".$this->wc->db->escape_string($this->name)."', ";
        $query  .=  "'".$contextString."', ";
        $query  .=  "'".$this->modificator->id."', "; 
        $query  .=  "'".$this->modification_date->sqlFormat()."', ";
        $query  .=  "'".$userID."', "; 
        $query  .=  "'".$currentDate."'";
        
        foreach( $orderAttributesKeys as $attributeName => $orderKeys ){
            foreach( $orderKeys as $key )
            {
                $value  =   $this->attributes[$attributeName]->values[$key];
                $query  .=  ", '".$this->wc->db->escape_string($value)."'";
            }
        }
        
        $query  .=  " ) ";
        
        $archiveId = $this->wc->db->insertQuery($query);
        
        $archive = new Archive($this->structure);
        $archive->fetch($archiveId);
        
        foreach( $this->attributes as $attribute ){
            $attribute->create($archive);
        }
        
        return $archiveId;
    }
    
    static function searchUserContentsData( WitchCase $wc, $structureName, $userID, $offset=false, $limit=false )
    {
        $query  =   "SELECT content.*, loc.* ";
        $query  .=  "FROM `content__".$structureName."` AS content, ";
        $query  .=  "`localisation` AS loc ";
        
        $query  .=  "WHERE ( content.creator=".$userID." ";
        $query  .=      "OR content.modificator=".$userID." ) ";
        $query  .=  "AND loc.site='".$wc->website->name."' ";
        $query  .=  "AND loc.target_table='content__".$structureName."' ";
        $query  .=  "AND loc.target_fk=content.id ";
        
        $query  .=  "ORDER BY content.modification_date DESC ";
        
        if( $offset !== false && is_int($offset)
            && $limit && is_int($limit)
        ){
            $query  .=  "LIMIT ".$offset.", ".$limit." ";
        }
        
        return $wc->db->selectQuery($query);
    }
}
