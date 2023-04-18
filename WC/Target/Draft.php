<?php
namespace WC\Target;

use WC\WitchCase;
use WC\Target;
use WC\Datatype\Signature;
use WC\Datatype\ExtendedDateTime;

class Draft extends Target 
{    
    static $dbFields    =   array(
                                "`content_key` int(11) DEFAULT NULL",
                                "`creator` int(11) DEFAULT NULL",
                                "`creation_date` datetime DEFAULT NULL",
                                "`modificator` int(11) DEFAULT NULL",
                                "`modification_date` datetime DEFAULT NULL",
                            );
    
    static $datatypes   =   array(
                                'Signature'         =>  array(
                                                            'creator', 
                                                            'modificator'
                                                        ),
                                'ExtendedDateTime'  =>  array(
                                                            'creation_date', 
                                                            'modification_date'
                                                        )
                            );
    
    function __construct( WitchCase $wc, $structure )
    {
        $this->type                 =   'draft';
        $this->structure            =   $structure;
        $this->content_key          =   0;
        $this->creator              =   new Signature('', '', '');
        $this->creation_date        =   new ExtendedDateTime("0000-00-00 00:00:00");
        $this->modificator          =   new Signature('', '', '');
        $this->modification_date    =   new ExtendedDateTime("0000-00-00 00:00:00");
        
        parent::__construct($wc, "draft_".$structure);
    }
    
    function fetch( $id )
    {
        return $this->fetchTarget( $id, self::$datatypes );
    }
    
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
    
    function publish( $args=[] )
    {
        $this->wc->db->begin();
        
        if( count($args) > 0 && !$this->edit($args) )
        {
            $message    = "Cannot save ".$this->name." ".$this->structure;
            $message   .= " draft of ID: ".$this->id;
            $message   .= ", aborting publication ";
            
            $this->wc->log->error($message);
            $this->wc->db->rollback();
            return false;
        }
        
        if( $this->content_key && !$this->publishUpdate($args) )
        {
            $this->wc->db->rollback();
            return false;
        }
        elseif( !$this->content_key && !$this->publishNew() ) 
        {
            $this->wc->db->rollback();
            return false;
        }
        
        return $this->wc->db->commit();
    }
    
    private function publishNew()
    {
        $contentTable   = "content_".$this->structure;
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
        
        $query  =   "UPDATE `".$this->wc->db->escape_string("content_".$this->structure)."` ";
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
    
    function delete()
    {
        if( !$this->content_key )
        {
            //if( Localisation::deleteFromTarget($this->table, $this->id) === false ){
            if( false ){
                return false;
            }
            else
            {
                $query  =   "DELETE FROM `".$this->wc->db->escape_string($this->table)."` ";
                $query  .=  "WHERE `id` = '".$this->wc->db->escape_string($this->id)."' ";
                
                if( !$this->wc->db->deleteQuery($query) )
                {
                    $message = "Localisation deleted but not draft ".$this->table." of ID ".$this->id;
                    $this->wc->log->error($message);
                    return $message;
                }
                
                foreach( $this->attributes as $attribute )
                {   $attribute->delete();   }
                
                return true;
            }
        }
        else
        {
            $archiveTable   = "archive_".$this->structure;
            $archiveID      = $this->archive();
            
            if( !$archiveID )
            {   return false;   }
            
            //if( !Localisation::changeTarget( $this->table, $this->id, $archiveTable, $archiveID ) )
            if( false )
            {   return false;   }
            
            $query  =   "DELETE FROM `".$this->table."` ";
            $query  .=  "WHERE id='".$this->id."' ";
            
            return $this->wc->db->deleteQuery( $query );
        }
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
        
        $query  .=  "INNER JOIN `content_".$structure."` AS content ";
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
        $query  .=          "AND loc.target_table='content_".$structureName."' ) ";
        $query  .=  ") ";
        
        $query  .=  "ORDER BY draft.modification_date DESC ";
        
        return $wc->db->selectQuery($query);
    }
}