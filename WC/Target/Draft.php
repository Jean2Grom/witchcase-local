<?php
namespace WC\Target;

use WC\WitchCase;
use WC\Target;
use WC\TargetStructure;
use WC\Datatype\Signature;
use WC\Datatype\ExtendedDateTime;

class Draft extends Target 
{
    const TYPE = 'draft';
    
    static $dbFields = [
        "`content_key` int(11) DEFAULT NULL",
    ];   
    
    var $content_key = false;
    
    function set( $args )
    {
        return $this->setTarget( $args, self::$datatypes );
    }
    
    function createFromContent( $targetLocalisation )
    {
        $currentDate                = date("Y-m-d H:i:s");
        $userID                     = $this->wc->user->id;
        $content                    = $targetLocalisation->getTarget();
        
        $this->content_key          =  $content->id;
        $this->name                 =  $content->name;
        $this->context              =  $content->context;
        
        $this->creator              =  $userID;
        $this->creation_date        =  new ExtendedDateTime($currentDate);
        $this->modificator          =  $userID;
        $this->modification_date    =  new ExtendedDateTime($currentDate);
        $this->attributes           =  $content->attributes;
        
        $query  =   "INSERT INTO `".$this->wc->db->escape_string($this->table)."` ";
        $query  .=  "(`content_key`, `name`, `context`, `creator`, ";
        $query  .=  "`creation_date`, `modificator`, `modification_date`";
        
        $orderAttributesKeys = [];
        foreach( $content->attributes as $attributeName => $attribute )
        {
            $orderAttributesKeys[$attributeName] = array();
            foreach($attribute->tableColumns as $key => $tableColumn)
            {
                $orderAttributesKeys[$attribute->name][] = $key;  
                $query .= ", `".$tableColumn."`";
            }
        }
        $query  .=  " ) ";
        
        if(is_array($this->context) )
        {
            $buffer = [];
            foreach($this->context as $label => $value){
                $buffer[] = $label.":".$value;
            }
            
            $contextString = implode(",", $buffer);
        }
        else {
            $contextString = $this->context;
        }
        
        $query  .=  "VALUES ( '".$this->content_key."', ";
        $query  .=  "'".$this->wc->db->escape_string($this->name)."', ";
        $query  .=  "'".$contextString."', ";
        $query  .=  "'".$userID."', "; 
        $query  .=  "'".$this->creation_date->sqlFormat()."', ";
        $query  .=  "'".$userID."', "; 
        $query  .=  "'".$this->modification_date->sqlFormat()."'";
        
        foreach( $orderAttributesKeys as $attributeName => $orderKeys ){
            foreach( $orderKeys as $key )
            {
                $value  =   $content->attributes[$attributeName]->values[$key];
                $query  .=  ", '".$this->wc->db->escape_string($value)."'";
            }
        }
        
        $query  .=  " ) ";
        
        $this->id = $this->wc->db->insertQuery($query);
        
        foreach( $content->attributes as $label => $contentAttribute ){
            $contentAttribute->create($this);
        }
        
        return $this->id;
    }
    
    function create( $parentsID, $module, $name, $description='', $customUrl=false, $newSite=false )
    {
        $this->wc->db->begin();
        
        $currentDate        = date("Y-m-d H:i:s");
        $userID             = $this->wc->user->id;
        $userName           = $this->wc->user->name;
        
        // Creation and Saving of the new draft
        $this->content_key          =  0;
        $this->name                 =  $name;
        $this->context              =  "";
        $this->creator              =  new Signature(   'creator', 
                                                        $userID, 
                                                        $userName
                                       );
        $this->creation_date        =  new ExtendedDateTime($currentDate);
        $this->modificator          =  new Signature(   'modificator', 
                                                        $userID, 
                                                        $userName
                                       );
        $this->modification_date    =  new ExtendedDateTime($currentDate);
        
        $query  =   "INSERT INTO `".$this->wc->db->escape_string($this->table)."` ";
        $query  .=  "(`content_key`, `name`, `context`, `creator`, ";
        $query  .=  "`creation_date`, `modificator`, `modification_date` ) ";
        
        $query  .=  "VALUES ( '".$this->content_key."', ";
        $query  .=  "'".$this->wc->db->escape_string($this->name)."', ";
        $query  .=  "'".$this->context."', ";
        $query  .=  "'".$userID."', "; 
        $query  .=  "'".$currentDate."', ";
        $query  .=  "'".$userID."', "; 
        $query  .=  "'".$currentDate."' ) ";
        
        $this->id = $this->wc->db->insertQuery($query);
        
        if( !$this->id )
        {
            $message    = "Cannot create ".$this->structure." ".$this->name;
            
            $this->wc->log->error($message);
            $this->wc->db->rollback();
            return false;
        }
        
        foreach( $this->attributes as $attribute )
        {   $attribute->create($this);  }
        
//        $samesiteNewLocationId =    Location::create(   $parentsID, 
//                                                        $name, 
//                                                        $module, 
//                                                        $this->table, 
//                                                        $this->id, 
//                                                        $description, 
//                                                        $customUrl, 
//                                                        $newSite
//                                    );
        
        $this->wc->db->commit();
        
        return $samesiteNewLocationId;
    }
    
    function publish()
    {
//        $this->wc->dump($this->id);
//        $this->wc->dump($this->content_key);
//        $this->wc->dump($this->structure->table);
//        $this->wc->dump($this->wc->user->id);
//        
//        $userID         = $this->wc->user->id;
//        
        
        $this->wc->db->begin();
        try {
            if( !$this->content_key )
            {
                $structure      = new TargetStructure( $this->wc, $this->structure->name, 'content' );            
                $content        = Target::factory( $this->wc, $structure );            
                $content->id    = $this->structure->createTarget($this->name, 'content');

                $content->name          = $this->name;
                $content->attributes    = $this->attributes;            
                $content->save();

                foreach( $this->getWitches() as $witch ){
                    $witch->edit(['target_table' => $content->structure->table, 'target_fk' => $content->id]);
                }            
            }


            $this->delete( false );

            $this->wc->db->commit();
        }
        catch( \Exception $e )
        {
            $this->wc->log->error($e->getMessage());
            $this->wc->db->rollback();
            return false;
        }
        
        return true;

    }
    
    private function publishNew()
    {
        $contentTable   = "content__".$this->structure;
        $currentDate    = date("Y-m-d H:i:s");
        $userID         = $this->wc->user->id;
        
        if( !$userID )
        {
            $this->wc->log->error( "Cannot get current user, SESSION var seems empty : aborting publication" );
            return false;
        }
        
        // Create new content
        $query  =   "INSERT INTO `".$this->wc->db->escape_string($contentTable)."` ";
        $query  .=  "( `name`, `context`, `creator`, ";
        $query  .=  "`publication_date`, `modificator`, `modification_date`";
        
        foreach( $this->attributes as $attribute ){
            foreach( $attribute->tableColumns as $key => $tableColumn  ){
                if( strcmp($attribute->values[$key], "__last_value__") != 0 ){
                    $query  .=  ", `".$tableColumn."`";
                }
            }
        }
        
        $query  .=  " ) ";
        
        $query  .=  "VALUES ( '".$this->wc->db->escape_string($this->name)."', ";
        $query  .=  "'".$this->wc->db->escape_string("")."', ";
        $query  .=  "'".$userID."', "; 
        $query  .=  "'".$currentDate."', ";
        $query  .=  "'".$userID."', "; 
        $query  .=  "'".$currentDate."'";
        
        foreach( $this->attributes as $attribute ){
            foreach( $attribute->tableColumns as $key => $tableColumn  ){
                if( strcmp($attribute->values[$key], "__last_value__") != 0 )
                {
                    $value  =   $attribute->values[$key];
                    $query  .=  ", '".$this->wc->db->escape_string($value)."'"; 
                }
            }
        }
        
        $query  .=  " ) ";
        
        $this->content_key = $this->wc->db->insertQuery($query);
        
        if( !is_numeric($this->content_key) )
        {
            $message    = "Cannot insert content ".$this->name;
            $message   .= ", aborting publication ";
            $this->wc->log->error($message);
            
            return false;
        }
        
        $content = new Content( $this->wc, $this->structure );
        
        $content->fetch($this->content_key);
        
        foreach( $this->attributes as $attribute ){
            $attribute->create($content);
        }
        
        // Update locations
        $query  =   "UPDATE `localisation` ";
        $query  .=  "SET `target_table` = '".$this->wc->db->escape_string($contentTable)."', ";
        $query  .=  "`target_fk` = '".$this->content_key."', ";
        //$query  .=  "`name` = '".$this->wc->db->escape_string($this->name)."', ";
        $query  .=  "`datetime` = '".$currentDate."' ";
        $query  .=  "WHERE `target_table` = '".$this->wc->db->escape_string($this->table)."' ";
        $query  .=  "AND `target_fk` = '".$this->id."' ";
        
        /*$query  .=  "AND ( ";
        
        $first = true;
        foreach( $this->wc->localisation->getAdministratedSites() as $administratedSite )
        {
            if( $first )
            {   $first = false; }
            else
            {   $query  .=  "OR ";  }
            
            $query  .=  "`site` = '".$this->wc->db->escape_string($administratedSite)."' ";
        }
        
        $query  .=  ") ";*/
        
        if( !$this->wc->db->updateQuery($query) )
        {
            $message    = "Cannot update localisations for content ".$this->name;
            $message   .= ", aborting publication ";
            $this->wc->log->error($message);
            
            return false;
        }
        
        // Delete draft
        $query  =   "DELETE FROM `".$this->wc->db->escape_string($this->table)."` ";
        $query  .=  "WHERE id = '".$this->wc->db->escape_string($this->id)."'";
        
        if( !$this->wc->db->deleteQuery($query) )
        {
            $this->wc->log->error("Delete current draft query failed, aborting publication");
            return false;
        }
        
        return true;
    }
    
    private function publishUpdate( $args )
    {
        // Archive content
        $content = new Content( $this->wc, $this->structure );
        
        if( !$content->fetch($this->content_key) )
        {
            $message    = "Cannot update content for draft ".$this->name." ID: ".$this->id;
            $message   .= " with ID: ".$this->content_key;
            $message   .= ", aborting publication ";
            $this->wc->log->error($message);
            
            return false;
        }
        
        $archiveID = $content->archive();
        
        if( !is_numeric($archiveID) )
        {
            $message    = "Cannot write Archive file for content ".$content->name;
            $message   .= " with ID: ".$content->id;
            $message   .= ", aborting publication ";
            $this->wc->log->error($message);
            
            return false;
        }
        
        // Update content
        $userID = $this->wc->user->id;
        
        if( !$userID )
        {
            $this->wc->log->error( "Cannot get current user, SESSION var seems empty : aborting publication" );
            return false;
        }
        
        $query  =   "UPDATE `".$this->wc->db->escape_string("content__".$this->structure)."` ";
        $query  .=  "SET `modificator` = '".$userID."', ";
        if( isset($args['name']) )
        {   $query  .=  "`name` = '".$this->wc->db->escape_string($this->name)."', "; }

        $query  .=  "`modification_date` = '".date("Y-m-d H:i:s")."'";

        foreach( $this->attributes as $attribute )
        {   foreach( $attribute->tableColumns as $key => $tableColumn  )
            {   if( strcmp($attribute->values[$key], "__last_value__") != 0 )
                {
                    $value  =   $this->wc->db->escape_string($attribute->values[$key]);
                    $query  .=  ", `".$tableColumn."` = '".$value."'";
        }   }   }

        $query  .=  " WHERE `id` = '".$this->content_key."' ";

        if( !$this->wc->db->updateQuery($query) )
        {
            $message    = "Cannot update content ".$content->name;
            $message   .= " with ID: ".$content->id;
            $message   .= ", aborting publication ";
            $this->wc->log->error($message);
            
            return false;
        }
        
        foreach( $content->attributes as $attribute )
        {
            $attribute->set($args);
            $attribute->save($content);
        }
        
        // Delete (archive) same content drafts
        $query  =   "DELETE FROM `".$this->wc->db->escape_string($this->table)."` ";
        $query  .=  "WHERE id = '".$this->wc->db->escape_string($this->id)."'";
        
        if( !$this->wc->db->deleteQuery($query) )
        {
            $this->wc->log->error("Delete current draft query failed, aborting publication");
            return false;
        }
        
        $query  =   "SELECT * FROM `".$this->wc->db->escape_string($this->table)."` ";
        $query  .=  "WHERE content_key = '".$this->wc->db->escape_string($this->content_key)."'";
        
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
        
        return true;
    }
    
    
    function archive( )
    {
        $userID = $this->wc->user->id;
        
        $currentDate = date("Y-m-d H:i:s");
        
        $query  =   "INSERT INTO `archive_".$this->wc->db->escape_string($this->structure)."` ";
        $query  .=  "(`content_key`, `name`, `context`, `last_modificator`, ";
        $query  .=  "`last_modification_date`, `archiver`, `archive_date`";
        
        $orderAttributesKeys = array();
        foreach( $this->attributes as $attributeName => $attribute )
        {
            $orderAttributesKeys[$attributeName] = array();
            foreach($attribute->tableColumns as $key => $tableColumn)
            {
                $orderAttributesKeys[$attribute->name][] = $key;  
                $query .= ", `".$tableColumn."`";
            }
        }
        $query  .=  " ) ";
        
        if( is_array($this->context) )
        {
            $buffer = array();
            foreach($this->context as $label => $value)
            {   $buffer[] = $label.":".$value;  }
            
            $contextString = implode(",", $buffer);
        }
        else
        {   $contextString = $this->context;    }
        
        $query  .=  "VALUES ( '".$this->content_key."', ";
        $query  .=  "'".$this->wc->db->escape_string($this->name)."', ";
        $query  .=  "'".$contextString."', ";
        $query  .=  "'".$this->modificator->id."', "; 
        $query  .=  "'".$this->modification_date->sqlFormat()."', ";
        $query  .=  "'".$userID."', "; 
        $query  .=  "'".$currentDate."'";
        
        foreach( $orderAttributesKeys as $attributeName => $orderKeys )
        {   foreach( $orderKeys as $key )
            {
                $value  =   $this->attributes[$attributeName]->values[$key];
                $query  .=  ", '".$this->wc->db->escape_string($value)."'";
        }   }
        
        $query  .=  " ) ";
        
        return $this->wc->db->insertQuery($query);
    }
    
    static function searchDrafts( WitchCase $wc, $structure, $localisationID )
    {
        $query  =   "SELECT draft.*";
        
        foreach( self::$datatypes['Signature'] as $column ){
            $query  .=  ", ".$column.".name AS ".$column."__signature";
        }
        
        $query  .=  " FROM ";
        
        foreach( self::$datatypes['Signature'] as $column ){
            $query  .=  "`user_connexion` AS ".$column.", ";
        }
        
        $query  .=  "`draft_".$structure."` AS draft ";
        
        $query  .=  "INNER JOIN `content__".$structure."` AS content ";
        $query  .=  "ON ( draft.content_key = content.id ";
        $query  .=  "AND draft.creation_date > content.modification_date ) ";
        $query  .=  "INNER JOIN `localisation` ";
        $query  .=  "ON content.id = localisation.target_fk ";
        $query  .=  "WHERE localisation.id = '".$wc->db->escape_string($localisationID)."' ";
        
        foreach( self::$datatypes['Signature'] as $column ){
            $query  .=  "AND ".$column.".id = draft.".$column." ";
        }
        
        $query  .=  "ORDER BY draft.modification_date DESC, draft.creation_date DESC";
        
        $currentDraftsData = $wc->db->multipleRowsQuery($query);
        
        if( $currentDraftsData === false )
        {
            $wc->log->error("Search Drafts query failed: \"".$query."\" possible corrupted Database !");
            return [];
        }
        
        $currentDrafts = [];
        foreach($currentDraftsData as $draftData)
        {
            $draft  = new self( $wc, $structure );
            
            $draft->set($draftData);
            $currentDrafts[] = $draft;
        }
        
        return $currentDrafts;
    }
    
    static function searchUserDraftsData( WitchCase $wc, $structureName, $userID=false )
    {
        if( $userID === false ){
            $userID = $wc->user->id;
        }
        
        $query  =   "SELECT draft.*, loc.* ";
        $query  .=  "FROM `draft_".$structureName."` AS draft, ";
        $query  .=  "`localisation` AS loc ";
        
        $query  .=  "WHERE ( draft.creator=".$userID." ";
        $query  .=      "OR draft.modificator=".$userID." ) ";
        $query  .=  "AND loc.site='".$wc->website->name."' ";
        
        $query  .=  "AND (  ";
        $query  .=      "( loc.target_table='draft_".$structureName."'  ";
        $query  .=          "AND loc.target_fk=draft.id ) ";
        $query  .=      "OR ( loc.target_fk=draft.content_key ";
        $query  .=          "AND loc.target_table='content__".$structureName."' ) ";
        $query  .=  ") ";
        
        $query  .=  "ORDER BY draft.modification_date DESC ";
        
        return $wc->db->selectQuery($query);
    }
}