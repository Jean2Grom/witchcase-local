<?php

namespace WC;

use WC\Module;
use WC\User;

//use WC\Targets\Draft;
//use WC\Targets\Archive;
//use WC\Targets\Content;

class Localisation
{
    var $site;
    var $siteAccess;
    var $data;
    
    var $module;
    var $moduleName;
    var $id;
    var $url;
    var $status;
    var $name;
    var $description;
    var $is_main;
    var $context;
    var $datetime;
    var $priority;
    
    var $position;
    var $depth;
    var $maxDepth;
    
    var $target;
    var $has_target;
    var $target_table;
    var $target_fk;
    var $target_type;
    var $target_structure;
    
    var $accessDenied;
    var $children;
    var $parents;
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc, $id=false, $data=false )
    {
        $this->wc = $wc;
        
        // Access localisation from URI
        if( !$id )
        {
            $script_uri     = filter_input(INPUT_SERVER, "SCRIPT_URI");
            $parsed_url     = parse_url( strtolower($script_uri) );
            
            // Determinating which site is acceded comparing
            // Configuration and URI
            $this->site     = false;
            $matchDegree    = 0;
            foreach( $this->wc->configuration->getSiteAccessMap() as $siteAccess => $site )
            {
                $access         = $parsed_url["host"].$parsed_url['path'];
                
                if( strpos($access, $siteAccess) === 0 && strlen($access) > $matchDegree )
                {
                    $matchDegree        = strlen($siteAccess);
                    $this->site         = $site;
                    $this->siteAccess   = $siteAccess;
                    
                    $url_field          = substr($access, $matchDegree);
                    if( (strcmp(substr($url_field, -1), "/") == 0) 
                        && (strcmp($url_field, "/") != 0)   
                    ){   
                        $url_field = substr($url_field, 0, -1);
                    }
                    if( strlen($url_field) == 0 ){
                        $url_field = "/";   
                    }
                }
            }
            
            if( !$this->site ){   
                $this->wc->log->error("Site access is not in configuration file", true);  
            }
            else
            {
                $message = "Accessing site: ".$this->site.", with site access: ".$this->siteAccess;
                $this->wc->debug->dump($message);
            }
            

            /*
            $url_elements   = explode("/", $parsed_url['path']);
            $access         = $parsed_url["host"]."/".$url_elements[1];
            
            // Determinating which site is acceded comparing
            // Configuration and URI
            $this->site = false;
            foreach( $this->wc->configuration->getSitesAccess() as $site => $siteAccessArray ){   
                foreach( $siteAccessArray as $siteaccess ){   
                    if( strcmp($siteaccess, $access) == 0 )
                    {
                        $this->site         = $site;
                        $this->siteAccess   = $siteaccess;
                        
                        // Unsetting site access from url elements
                        unset($url_elements[1]);
                        break 2;
                    }
                    elseif( strcmp($parsed_url["host"], $siteaccess) == 0 )
                    {
                        $this->site         =  $site;
                        $this->siteAccess   = $siteaccess;
                    }   
                } 
            }
            
            if( !$this->site ){   
                $this->wc->log->error("Site access is not in configuration file", true);  
            }
            else
            {
                $message = "Accessing site: ".$this->site.", with site access: ".$this->siteAccess;
                $this->wc->debug->dump($message);
            }
            
            // Getting localisation datas
            $url_field = implode("/", $url_elements);
            
            if( (strcmp(substr($url_field, -1), "/") == 0) 
                && (strcmp($url_field, "/") != 0)   
            ){   
                $url_field = substr($url_field, 0, -1);
            }
            
            if( strlen($url_field) == 0 ){
                $url_field = "/";   
            }
            */
            
            $query  =   "SELECT * FROM localisation ";
            $query  .=  "WHERE site = '".$this->wc->db->escape_string($this->site)."' ";
            $query  .=  "AND url = '".$this->wc->db->escape_string($url_field)."' ";
            
            $data = $this->wc->db->fetchQuery($query);
            
            if( $data === false ){   
                $this->wc->log->error("Query error in implementing Localisation access", true);   
            }
            elseif( !is_array($data) && $data != 0 ){   
                $this->wc->log->error("Database corrupted, multiple location for URI ".$script_uri, true);    
            }
            elseif( !is_array($data) && $data == 0 )
            {
                $this->moduleName = '404';
                $this->wc->debug->dump("Localisation not found, accessing module 404");
            }
            else{
                $this->wc->debug->dump("Accessing localisation: ".$data['name'].", at URL: ".$data['url']);
            }
        }
        elseif( !$data ) // Get localisation from ID
        {
            $query  =   "SELECT * FROM localisation ";
            $query  .=  "WHERE id = '".$this->wc->db->escape_string($id)."' ";
            
            $data = $this->wc->db->fetchQuery($query);
            if( $data === false ){
                $this->wc->log->error("Query error in implementing Localisation from ID: ".$id);
            }
        }
        
        $this->data = $data;
        
        $this->position     = [];
        $this->depth        = 0;
        $this->maxDepth     = 0;
        $this->has_target   = false;
        if( is_array($data) )
        {
            foreach( $data as $label => $value ){
                if( strcmp("level_", substr($label, 0, 6)) == 0 )
                {
                    $this->maxDepth++;
                    
                    if( !is_null($value) )
                    {
                        $level = (int) substr($label, 6);
                        $this->position[$level] = (int) $value;
                        
                        $this->depth = $level;
                    }
                }
                elseif( strcmp("datetime", $label) == 0 ){
                    $this->datetime = new \DateTime($value);
                }
                elseif( strcmp("status", $label) == 0 ){
                    $this->status = $this->wc->configuration->read('global', "status.".$value );
                }
                elseif( strcmp("module", $label) == 0 ){
                    $this->moduleName = $value;
                }
                else {
                    $this->$label = $value;
                }
            }
            
            if( $data['target_table'] && !is_null($data['target_fk']) ){
                $this->has_target = true;
            }
        }
        
        if( $this->has_target )
        {
            $buffer = explode("_", $this->target_table);
            $this->target_type      = $buffer[0];
            unset($buffer[0]);
            $this->target_structure = implode("_", $buffer);
        }
    }
    
    function getModule()
    {
        if( !empty($this->module) ){
            return $this->module;
        }
        
        if( strcmp($this->moduleName, '404') == 0 ){
            $permission = true;
        }
        else // Is the current user has permission to access module ?
        {
            $permission = false;
            foreach( $this->wc->user->policies as $police ){
                if( strcmp($police['module'], $this->moduleName) == 0 
                    || strcmp($police['module'], '*') == 0
                ){
                    $matchPosition = true;
                    
                    foreach( $police["position"] as $level => $positionID )
                    {
                        if( isset($this->position[$level])
                            && $positionID == $this->position[$level] 
                        ) {
                            continue;
                        }
                        elseif( strcmp('*', $positionID) == 0 ){
                            break;
                        }
                        else
                        {
                            $matchPosition = false;
                            break;
                        }
                    }
                    
                    if( $matchPosition )
                    {
                        $permission = true;
                        break;
                    }
                }
            }
        }
        
        if( !$permission )
        {
            $this->accessDenied = $this->moduleName;
            $this->moduleName   = 'login';
            $this->has_target   = false;
            $this->wc->debug->dump("Access denied for user: ".$this->wc->user->name.", redirecting to login.");
        }
        else{
            $this->accessDenied = false;
        }
        
        $this->module = new Module( $this );
        
        return $this->module;
    }
    
    function getTarget( $mandatory=true )
    {
        if( !$this->has_target ){
            return false;
        }
        elseif( !empty($this->target) ){
            return $this->target;
        }
        
        $targetClass    = "WC\\Targets\\".ucfirst($this->target_type);
        
$this->wc->debug->dump( $targetClass, 'TARGET CLASS' );        
        
        $target         = new $targetClass( $this->wc, $this->target_structure );
        
        $fetchResult    = $target->fetch( $this->target_fk ); 
        
        if( !$fetchResult )
        {
            $message = "Can't fetch target: ".$this->target_fk." of table: ".$this->target_table;
            $this->wc->log->error($message, $mandatory);
        }
        else
        {
            $message    =   "Target : ".$target->name;
            $message    .=  "\nType : ".$this->target_type;
            $message    .=  "\nStructure : ".$target->structure;
            $this->wc->debug->dump($message);
        }
        
        $this->target = $target;
        
        return $this->target;
    }
    
    function children( $viewDraft=false, $viewArchive=false, $orders=[], $offset=0, $limit=false )
    {
        if( isset($this->children) ){
            return $this->children;
        }
        
        $this->children = [];
        if( $this->depth == $this->maxDepth ){
            return $this->children;
        }
        
        if( !isset($orders['priority']) ){
            $orders['priority'] = 'asc';
        }
        
        if( !isset($orders['id']) ){
            $orders['id'] = 'asc';
        }
        
        $query  =   "SELECT * FROM localisation ";
        $query  .=  $this->childrenCondition( $viewDraft, $viewArchive );
        
        $query  .=  "ORDER BY ";
        
        $first  =   true;
        foreach( $orders as $field => $order )
        {
            if( $first ){
                $first  =   false;
            }
            else {
                $query  .=  ", ";
            }
            
            $query  .=  "`".$field."` ".$order; 
        }
        
        if( $limit ){
            $query  .=  " LIMIT ".$offset.", ".$limit;
        }
        
        $childrenData   = $this->wc->db->multipleRowsQuery($query);
        
        foreach( $childrenData as $childData){
            $this->children[] = new self( $this->wc, $childData['id'], $childData );
        }
        
        return $this->children;
    }
    
    function childrenCount( $viewDraft=false, $viewArchive=false )
    {
        if( isset($this->childrenCount) )
        {   return $this->childrenCount; }
        
        $this->childrenCount = 0;
        if( $this->depth == $this->maxDepth )
        {   return $this->childrenCount; }
        
        $query  =   "SELECT count(*) FROM localisation ";
        $query  .=  $this->childrenCondition( $viewDraft, $viewArchive );
        
        $this->childrenCount = $this->wc->db->fetchQuery($query)["count(*)"];
        
        return $this->childrenCount;
    }
    
    private function childrenCondition( $viewDraft, $viewArchive )
    {
        $condition =  "WHERE site='".$this->site."' ";
        
        if( !$viewDraft && !$viewArchive ){
            $condition  .=  "AND target_table LIKE 'content_%' ";
        }
        elseif( !$viewDraft ){
            $condition  .=  "AND target_table NOT LIKE 'draft_%' ";
        }
        elseif( !$viewArchive ){
            $condition  .=  "AND target_table NOT LIKE 'archive_%' ";
        }
        
        foreach( $this->position as $level => $id ){
            $condition  .=  "AND level_".$level." = '".$id."' ";
        }
        
        $condition  .=  "AND level_".($this->depth+1)." IS NOT NULL ";
        
        for( $i=$this->depth+2; $i<=$this->maxDepth; $i++  ){
            $condition  .=  "AND level_".$i." IS NULL ";
        }
        
        return $condition;
    }
    
    function isChild( array $position, $includeSelf = false )
    {
        if( count($this->position) < count($position)  ){
            return false;
        }
        
        if( $includeSelf
            && count( array_diff($this->position, $position) ) == 0
        ){
            return true;
        }
        
        foreach( $position as $level => $positionId ){
            if( $this->position[$level] != $positionId ){
                return false;
            }
        }
        
        return true;
    }
    
    function parents()
    {
        if( $this->parents )
        {   return $this->parents;  }
        
        if( count($this->position) == 0 )
        {
            $this->parents = array();
            return $this->parents;
        }
        
        $query  =   "SELECT * FROM localisation ";
        $query  .=  "WHERE ";
        
        $orderBy = "";
        foreach( $this->position as $level => $id )
        {
            $query  .=  " (";
            $first = true;
            for( $i=1; $i<=$this->maxDepth; $i++  )
            {
                if($first)
                {   $first = false; }
                else
                {   $query  .=  "AND "; }
                
                if( ($i<$level) && ($i!=$this->depth) )
                {   $query  .=  "level_".$i." = '".$this->position[$i]."' ";    }
                else
                {   $query  .=  "level_".$i." IS NULL ";    }
            }
            
            $query  .=  ") ";
            
            if( $level < $this->depth )
            {
                $query .= "OR ";
                
                if( strlen($orderBy) != 0 )
                {   $orderBy .= ", ";   }
                
                $orderBy .= "level_".$level." DESC";
            }
        }
        
        if( strlen($orderBy) != 0 )
        {   $query  .=  "ORDER BY ".$orderBy;   }
        
        $this->parents = $this->wc->db->multipleRowsQuery($query);
        
        return $this->parents;
    }
    
    function sameTarget()
    {
        $query  =   "SELECT * ";
        $query  .=  "FROM localisation ";
        $query  .=  "WHERE target_table= '".$this->target_table."' ";
        $query  .=  "AND target_fk= '".$this->target_fk."' ";
        
        return $this->wc->db->multipleRowsQuery($query);
    }
    
    function sameTargetLocations()
    {
        $module         = $this->moduleName;
        $target_table   = $this->target_table;
        $target_fk      = $this->target_fk;
        $location       = $this->location_id;
        
        $query  =   "SELECT * ";
        $query  .=  "FROM localisation ";
        $query  .=  "WHERE module= '".$module."' ";
        
        if( $this->has_target )
        {
            $query  .=  "AND target_table= '".$target_table."' ";
            $query  .=  "AND target_fk= '".$target_fk."' ";
        }
        else
        {
            $query  .=  "AND ( target_fk IS NULL ";
            $query  .=  "OR target_fk='0' ) ";
        }
        
        $unorderedLocationsData = $this->wc->db->multipleRowsQuery($query);
        
        $locations = [ $location => [] ];
        foreach( $this->getAdministratedSites() as $site ) 
        {   foreach( $unorderedLocationsData as $unorderedLocationsDataItem )
            {   if( strcmp($unorderedLocationsDataItem['site'], $site) == 0 )
                {
                    if( !isset($locations[ $unorderedLocationsDataItem['location_id'] ]) )
                    {   $locations[ $unorderedLocationsDataItem['location_id'] ] = [];  }
                    
                    $locations[ $unorderedLocationsDataItem['location_id'] ][] = $unorderedLocationsDataItem;
        }   }   }
        
        return $locations;
    }
    
    function getLocation()
    {
        if( isset($this->location) ){
            return $this->location;
        }
        
        $this->location = Location::getFromLocalisationID( $this->wc, $this->id );
        
        return $this->location;
    }
    
    function getAdministratedSites()
    {
        if( isset($this->administratedSites) ){
            return $this->administratedSites;
        }
        
        $this->administratedSites = $this->wc->configuration->getAdministratedSites( $this->site );
        
        return $this->administratedSites;
    }
    
    function delete( $targetDeletion=true )
    {
        $deletedLocationIDs = [];
        foreach( $this->children(true, true) as $deleteLocalisation )
        {
            $deletedLocationIDs =   array_merge(
                                        $deletedLocationIDs, 
                                        $deleteLocalisation->delete()
                                    );
        }
        
        if( $this->has_target && $targetDeletion )
        {
            $targetDeletion = true;
            
            // IF MORE LOCATION HAS SAME TARGET : DELETE LOCALISATION
            foreach( $this->sameTarget() as $sameTargetLocationData )
            {   if( strcmp($sameTargetLocationData['location_id'], $this->location_id) != 0 )
                {
                    $targetDeletion = false;
                    break;
            }   }
        }
        else 
        {   $targetDeletion = false;    }
        
        if( !$targetDeletion )
        {
            $query  =   "DELETE FROM `localisation` ";
            $query  .=  "WHERE location_id='".$this->location_id."'";
            
            $this->wc->db->deleteQuery($query);
        }
        // ELSE DELETE TARGET AND CHANGE LOCALISATION TARGET
        else
        {
            $target = $this->getTarget();
            $target->delete();
        }
        
        $deletedLocationIDs[] = $this->id;
        
        return $deletedLocationIDs;
    }
    
    static function deleteFromTarget( $target_table, $target_fk )
    {
        $query  =   "SELECT * FROM `localisation` ";
        $query  .=  "WHERE `target_table` = '".$this->wc->db->escape_string($target_table)."' ";
        $query  .=  "AND `target_fk` = '".$this->wc->db->escape_string($target_fk)."' ";
        
        $deletedLocalisationIdArray = [];
        foreach( $this->wc->db->selectQuery($query) as $data )
        {   if( !in_array($data['id'], $deletedLocalisationIdArray) )
            {
                $deleteLocalisation         = new self($data['id'], $data);
                $deletedLocalisationResult  = $deleteLocalisation->delete(false);
                
                if( $deletedLocalisationResult === false )
                {   return false;   }
                
                $deletedLocalisationIdArray =   array_merge(    $deletedLocalisationIdArray, 
                                                                $deletedLocalisationResult
                                                );
        }   }
        
        return true;
    }
    
    function edit( $url=false, $name='' , $description='' )
    {
        $query  =   "UPDATE `localisation` ";
        $query  .=  "SET name='".$this->wc->db->escape_string($name)."', ";
        $query  .=  "description='".$this->wc->db->escape_string($description)."' ";
        
        if( $url )
        {
            $url = $this->testUrl($this->site, $url);
            $query  .=  ", url='".$this->wc->db->escape_string($url)."' ";
        }
        
        $query  .=  "WHERE id='".$this->id."' ";
        
        if( $this->wc->db->updateQuery($query) )
        {
            if( strcmp($this->url, $url) != 0 )
            {   foreach( $this->children(true, true) as $childLocalisation )
                {
                    $childNewUrl =  $url.substr(    $childLocalisation->url, 
                                                    strrpos($childLocalisation->url, '/') 
                                    );
                    
                    $childLocalisation->edit(   $childNewUrl, 
                                                $childLocalisation->name, 
                                                $childLocalisation->description );
            }   }
            
            $this->name         = $name;
            $this->url          = $url;
            $this->description  = $description;
            
            return true;
        }
        else
        {   return false;   }
    }
    
    function addChild(  $uniqueId, 
                        $name, 
                        $module='view', 
                        $target_table='', 
                        $target_fk=null, 
                        $description='', 
                        $customUrl='', 
                        $newSite=false  )

    {
        if( $newSite )
        {   $site = $newSite;    }
        else
        {   $site = $this->site;    }
        
        // Make new Url
        $urlGlue = '/';
        if( $this->url == '/' )
        {   $urlGlue = '';  }
        
        $url = $this->testUrl($site, $this->url.$urlGlue.$customUrl);
        
        // GET NEW POSITION
        $depth = $this->depth + 1;
        if( !$this->checkDepth($depth) )
        {   
            return false;
        }
        
        $max = $this->getMaxChildrenIndex();
        
        $position = $this->position;
        $position[$depth] = $max + 1;
        
        $query  =   "INSERT INTO `localisation` ";
        $query  .=  "(`site`, `url`, `module`, `name`, `description`, ";
        $query  .=  "`target_table`, `target_fk`, `location_id`, `is_main`, `context`, `datetime` ";

        foreach( $position as $level => $levelPosition )
        {   $query  .=  ", `level_".$level."`"; }

        $query  .=  " ) ";
        $query  .=  "VALUES ( '".$site."', ";
        $query  .=  "'".$url."', ";
        $query  .=  "'".$module."', ";
        $query  .=  "'".$this->wc->db->escape_string($name)."', ";
        $query  .=  "'".$this->wc->db->escape_string($description)."', ";
        $query  .=  "'".$target_table."', ";
        $query  .=  "'".$target_fk."', ";
        $query  .=  "'".$uniqueId."', ";
        $query  .=  "'1', ";
        $query  .=  "NULL, ";
        $query  .=  "'".date("Y-m-d H:i:s")."' ";
        
        foreach( $position as $level => $levelPosition )
        {   $query  .=  ", '".$levelPosition."'";   }

        $query  .=  " ) ";
        
        return $this->wc->db->insertQuery($query);
    }
    
    private function checkDepth( $depth )
    {
        if( !is_int($depth) )
        {   return false;   }
        
        if( $depth <= $this->maxDepth )
        {   return true;    }
        
        if( ($depth - $this->maxDepth) > 1 )
        {   return false;   }
        
        $query  =   "ALTER TABLE `localisation` ";
        $query  .=  "ADD `level_".$depth."` INT(11) UNSIGNED NULL DEFAULT NULL ";
        
        return $this->wc->db->alterQuery($query);
    }
    
    private function getMaxChildrenIndex()
    {
        $depth = $this->depth + 1;
        
        $query  =   "SELECT `level_".$depth."` AS MAX FROM `localisation` ";
        $linkingCondition = "WHERE ";

        foreach($this->position as $level => $levelPosition )
        {
            $query  .=  $linkingCondition."`level_".$level."` = '".$levelPosition."' ";
            $linkingCondition = "AND ";
        }
        
        $query  .=  "ORDER BY `level_".$depth."` DESC LIMIT 1 ";
        
        $max = (int) $this->wc->db->fetchQuery($query)["MAX"];
        
        if( !is_numeric($max) )
        {   return false;   }
        else
        {   return $max;    }
    }
    
    function getAllData()
    {
        $query =    "SELECT * FROM localisation ";
        
        $siteQueryPart      = "";
        $adminSitesArray    = $this->getAdministratedSites();
        $arrayDiff          = array_diff( $this->wc->configuration->read('global', 'sites'), $adminSitesArray );
        
        if( count($arrayDiff) > 0 )
        {   foreach( $adminSitesArray as $adminSite )
            {
                if( strlen($siteQueryPart) > 0 )
                {   $siteQueryPart .= "OR ";    }
                
                $siteQueryPart .= "site LIKE '".$adminSite."' ";
        }   }
        
        if( $siteQueryPart )
        {   $query .=   "WHERE ".$siteQueryPart." ";   }
        
        $queryOrderBy = "ORDER BY ";
        
        for( $i=1; $i<=$this->maxDepth; $i++ )
        {
            if( $i > 1 )
            {   $queryOrderBy .= ", ";  }
            
            $queryOrderBy .= "level_".$i." ASC ";
        }
        
        return $this->wc->db->multipleRowsQuery($query.$queryOrderBy);
    }
    
    static function changeTarget( $sourceTargetTable, $sourceTargetId, $destTargetTable, $destTargetId )
    {
        $query  =   "UPDATE `localisation` ";
        $query  .=  "SET `target_table` = '".$destTargetTable."', ";
        $query  .=  "`target_fk` = '".$destTargetId."', ";
        $query  .=  "`datetime` = '".date("Y-m-d H:i:s")."' ";
        $query  .=  "WHERE `target_table` = '".$sourceTargetTable."' ";
        $query  .=  "AND `target_fk` = '".$sourceTargetId."' ";
        
        return $this->wc->db->updateQuery($query);
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
    
    static function changePriority( WitchCase $wc, $id, $priority )
    {
        if( !is_numeric( (int) $id) || !is_numeric( (int) $priority) ){
            return false;
        }
        
        $query  =   "UPDATE localisation ";
        $query  .=  "SET priority = '".$wc->db->escape_string($priority)."' ";
        $query  .=  "WHERE id = '".$wc->db->escape_string($id)."' ";
        
        return $wc->db->updateQuery($query);
    }
    
    static function sameTargetFromId( $id )
    {
        $query  =   "SELECT * FROM localisation AS target ";
        $query  .=  "LEFT JOIN localisation AS data ";
        $query  .=  "ON ( data.target_table=target.target_table "; 
        $query  .=  "AND data.target_fk=target.target_fk ) "; 
        $query  .=  "WHERE target.id = '".$this->wc->db->escape_string($id)."' ";
        
        return $this->wc->db->multipleRowsQuery($query);
    }
    
    function testUrl( $site, $url )
    {
        $query  =   "SELECT id FROM localisation ";
        $query  .=  "WHERE site='".$site."' ";
        $query  .=  "AND url='".$url."' ";
        
        $existingUrlLocalisation = $this->wc->db->fetchQuery($query);
        
        if( !$existingUrlLocalisation 
            || strcmp($existingUrlLocalisation["id"], $this->id) == 0
        )
        {   return $url;    }
        
        else
        {
            $urlLastChar = substr($url, -1);
            
            if( !is_numeric($urlLastChar) )
            {   return self::testUrl( $site, $url."2"); }
            
            else
            {
                $urlLastChar    = (int) $urlLastChar + 1;
                return self::testUrl( $site, substr($url, 0, -1).$urlLastChar );
            }
        }
    }
    
    static function getFromPosition( WitchCase $wc, $position )
    {
        $maxDepth = $wc->localisation->maxDepth;
        
        $query  =   "SELECT * FROM `localisation` "; 
        $query  .=  "WHERE ";
        
        for( $level=1; $level<=$maxDepth; $level++ )
        {
            if( $level > 1 ){
                $query  .=  "AND ";
            }
            
            $query  .=  "`level_".$level."` ";
            
            if( isset($position[$level]) ){
                $query  .=  "=".$position[$level]." ";
            }
            else {
                $query  .=  " IS NULL ";
            }
        }
        
        $data = $wc->db->fetchQuery($query);
        
        return new self( $wc, $data['id'], $data );
    }

}
