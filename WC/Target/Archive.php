<?php
namespace WC\Target;

use WC\Target;


class Archive extends Target 
{
    const TYPE      = 'archive';
    const DB_FIELDS = [
        "`content_key` int(11) DEFAULT NULL",
    ];
    const ELEMENTS = [ 
        'content_key',
    ];

    var $content_key;
    
    static $datatypes            =   array(
                                            'Signature'         =>  array(
                                                                        'last_modificator', 
                                                                        'archiver'
                                                                    ),
                                            'ExtendedDateTime'  =>  array(
                                                                        'last_modification_date', 
                                                                        'archive_date'
                                                                    )
                                        );
    
    function set( $args )
    {
        return $this->setTarget( $args, self::$datatypes );
    }    
    
    function restore()
    {
        $contentTable = "content_".$this->structure;
        $query = "SELECT * FROM `".$contentTable."` WHERE id = '".$this->content_key."' ";
        
        $contentData = $this->wc->db->fetchQuery($query);
        
        $userID = $this->wc->user->id;
        
        if( !$userID )
        {
            $this->wc->log->error( "Cannot get current user, SESSION var seems empty : aborting restoration" );
            return false;
        }
        
        $currentDate = date("Y-m-d H:i:s");
        
        if( $contentData )
        {
            // Archive content
            $content = new Content( $this->wc, $this->structure );
            
            $content->set($contentData);
            $content->archive();
            
            // Archive & delete drafts
            $draftTable = "draft_".$this->structure;
            $query = "SELECT * FROM `".$draftTable."` WHERE content_key = '".$this->content_key."' ";
            
            $draftsData = $this->wc->db->selectQuery($query);
            
            if( $draftsData )
            {
                $draft = new Draft( $this->structure );
                foreach( $draftsData as $draftsDataItem )
                {
                    $draft->set($draftsDataItem);
                    $draft->delete();
                }
            }
            
            // TODO Structure management
            
            
            // MAJ content
            $query  =   "UPDATE `".$this->wc->db->escape_string("content_".$this->structure)."` ";
            $query  .=  "SET `modificator` = '".$userID."', ";
            
            $query  .=  "`modification_date` = '".$currentDate."'";
            
            foreach( $this->attributes as $attribute )
            {   foreach( $attribute->tableColumns as $key => $tableColumn  )
                {   if( strcmp($attribute->values[$key], "__last_value__") != 0 )
                    {
                        // TODO STRUCTURE !!!!!!!!!!
                        
                        $value  =   $this->wc->db->escape_string($attribute->values[$key]);
                        $query  .=  ", `".$tableColumn."` = '".$value."'";
            }   }   }
            
            $query  .=  " WHERE `id` = '".$this->content_key."' ";
            
            if( !$this->wc->db->updateQuery($query) )
            {
                $message    = "Cannot update content ".$content->name;
                $message   .= " with ID: ".$content->id;
                $message   .= ", aborting retoration ";
                $this->wc->log->error($message);
                
                return false;
            }
            
            foreach( $content->attributes as $attribute )
            {
                $attribute->save($content);
            }
        }
        else
        {
            // Create new content
            $query  =   "INSERT INTO `".$this->wc->db->escape_string($contentTable)."` ";
            $query  .=  "( `name`, `context`, `creator`, ";
            $query  .=  "`publication_date`, `modificator`, `modification_date`";
            
            foreach( $this->attributes as $attribute )
            {   foreach( $attribute->tableColumns as $key => $tableColumn  )
                {   if( strcmp($attribute->values[$key], "__last_value__") != 0 )
                    {   $query  .=  ", `".$tableColumn."`"; }
            }   }
            
            $query  .=  " ) ";
            
            $query  .=  "VALUES ( '".$this->wc->db->escape_string($this->name)."', ";
            $query  .=  "'".$this->wc->db->escape_string("")."', ";
            $query  .=  "'".$userID."', "; 
            $query  .=  "'".$currentDate."', ";
            $query  .=  "'".$userID."', "; 
            $query  .=  "'".$currentDate."'";

            foreach( $this->attributes as $attribute )
            {   foreach( $attribute->tableColumns as $key => $tableColumn  )
                {   if( strcmp($attribute->values[$key], "__last_value__") != 0 )
                    {
                        $value  =   $attribute->values[$key];
                        $query  .=  ", '".$this->wc->db->escape_string($value)."'"; 
            }   }   }

            $query  .=  " ) ";            
            
            $newContentId = $this->wc->db->insertQuery($query);

            if( !is_numeric($newContentId) )
            {
                $message    = "Cannot insert content ".$this->name;
                $message   .= ", aborting retoration ";
                $this->wc->log->error($message);

                return false;
            }
            
            $content = new Content(  $this->wc, $this->structure );
            
            $content->fetch($newContentId);
            
            foreach( $this->attributes as $attribute )
            {   $attribute->create($content);   }
            
            // Update locations
            $query  =   "UPDATE `localisation` ";
            $query  .=  "SET `target_table` = '".$this->wc->db->escape_string($contentTable)."', ";
            $query  .=  "`target_fk` = '".$newContentId."', ";
            //$query  .=  "`name` = '".$this->wc->db->escape_string($this->name)."', ";
            $query  .=  "`datetime` = '".$currentDate."' ";
            $query  .=  "WHERE `target_table` = '".$this->wc->db->escape_string($this->table)."' ";
            $query  .=  "AND `target_fk` = '".$this->id."' ";
            
            /*$query  .=  "AND ( ";

            $first = true;
            foreach( $localisation->getAdministratedSites() as $administratedSite )
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
                $message   .= ", aborting retoration ";
                $this->wc->log->error($message);

                return false;
            }
            
            // MAJ linked archives
            $query  =   "UPDATE `".$this->wc->db->escape_string($this->table)."` ";
            $query  .=  "SET content_key = '".$this->wc->db->escape_string($newContentId)."' ";
            $query  .=  "WHERE content_key = '".$this->wc->db->escape_string($this->content_key)."' ";
            
            if( !$this->wc->db->updateQuery($query) )
            {
                $message    = "Cannot update linked archives ";
                $message   .= ", aborting retoration ";
                $this->wc->log->error($message);
                
                return false;
            }
        }
        
        $query  =   "DELETE FROM `".$this->wc->db->escape_string($this->table)."` ";
        $query  .=  "WHERE id = '".$this->wc->db->escape_string($this->id)."' ";
        
        if( !$this->wc->db->deleteQuery( $query ) )
        {
            $message    = "Cannot delete this archive ";
            $message   .= ", aborting retoration ";
            $this->wc->log->error($message);
            
            return false;
        }
        
        return true;
    }
    
    function publish()
    {
        return $this->restore();
    }
}
