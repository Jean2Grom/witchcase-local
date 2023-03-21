<?php
namespace WC;

use WC\Website\WitchSummoning;

/**
 * Description of Witch
 *
 * @author teletravail
 */
class Witch 
{
    const FIELDS = [
        "id",
        "name",
        "data",
        "site",
        "url",
        "status",
        "invoke",
        "target_table",
        "target_fk",
        "ext_id",
        "is_main",
        "context",
        "datetime",
        "priority",
    ];
    private $properties     = [];
    
    var $statusLevel        = 0;
    
    var $uri                = false;
    var $depth              = 0;
    var $position           = [];
    var $accessDeniedFor    = false;
    var $module             = false;
    var $modules            = [];
    
    var $mother;
    var $sisters            = [];
    var $daughters          = [];    
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc )
    {
        $this->wc = $wc;
        
        foreach( self::FIELDS as $field ){
            $this->properties[$field] = NULL;
        }
    }
    
    public function __set(string $name, mixed $value): void {
        $this->properties[$name] = $value;
    }
    
    public function __get(string $name): mixed {
        return $this->properties[$name];
    }
    
    static function createFromId( WitchCase $wc, int $id )
    {
        if( !empty($id) )
        {
            $query = "";
            $query  .=  "SELECT * FROM witch ";
            $query  .=  "WHERE id = ".( (integer) $id );
            
            $data = $wc->db->fetchQuery($query);
        }
        
        return self::createFromData( $wc, $data );
    }
    
    static function createFromData(  WitchCase $wc, array $data )
    {
        $witch = new self( $wc );
        
        foreach( $data as $field => $value ){
            $witch->{$field} = $value;
        }
        
        if( !empty($witch->datetime) ){
            $witch->datetime = new \DateTime($witch->datetime);
        }
        
        if( isset($witch->status) )
        {
            $witch->statusLevel = $witch->status;
            $witch->status      = $wc->configuration->read('global', "status")[ $witch->status ];
        }

        $witch->position    = [];
        
        $i = 1;
        //while( isset($witch->{'level_'.$i}) )
        //while( isset($witch->properties['level_'.$i]) )
        while( isset($data['level_'.$i]) )
        {
            $witch->position[$i] = (int) $witch->{'level_'.$i};
            $i++;
        }
        $witch->depth       = $i - 1; 
        
        //if( !empty($witch->url) )
        if( !empty($data['url']) )
        {
            $witch->uri = "";
            if( $wc->website->baseUri != '/' ){
                $witch->uri = $wc->website->baseUri;
            }
            $witch->uri .= $witch->url;
        }
        
        return $witch;
    }
    
    function setMother( self $mother )
    {
        $this->unsetMother();
        
        $this->mother = $mother;
        if( !in_array($this->id, array_keys($mother->daughters)) ){
            $mother->addDaughter($this);
        }
        
        return $this;
    }
    
    function unsetMother()
    {
        if( !empty($this->mother) && !empty($this->mother->daughters[ $this->id ]) ){
            unset($this->mother->daughters[ $this->id ]);
        }
        
        $this->mother = NULL;
        
        return $this;
    }
    
    function addSister( self $sister )
    {
        if( empty($this->sisters) ){
            $this->sisters = [];
        }
        
        if( $sister->id != $this->id ){
            $this->sisters[ $sister->id ] = $sister;
        }
        
        return $this;
    }
    
    function removeSister( self $sister )
    {
        if( !empty($this->sisters[ $sister->id ]) ){
            unset($this->sisters[ $sister->id ]);
        }
        
        if( !empty($sister->sisters[ $this->id ]) ){
            unset($sister->sisters[ $this->id ]);
        }
        
        return $this;
    }
    
    function listSistersIds()
    {
        $list = [];
        if( !empty($this->sisters) ){
            $list = array_keys($this->sisters);
        }
        
        return $list;
    }
    
    function reorderWitches( $witchesList )
    {
        $orderedWitchesIds = [];
        foreach( $witchesList as $witchItem ) 
        {
            $priority = 1000000000 - $witchItem->priority;
            
            for( $i=strlen($priority); $i < 10; $i++  ){
                $priority = "0".$priority;
            }
            
            $orderIndex = $priority."__".mb_strtolower($witchItem->name)."__".$witchItem->id;
            $orderedWitchesIds[ $orderIndex ] = $witchItem->id;
        }
        
        ksort($orderedWitchesIds);
        
        $orderedWitches = [];
        foreach( $orderedWitchesIds as $orderedWitchId ){
            $orderedWitches[ $orderedWitchId ] = $witchesList[ $orderedWitchId ];
        }
        
        return $orderedWitches;
    }
    
    function fetchDaughters()
    {
        $configuration = [
            'self' => [
                'id'    => $this->id,
                'craft' => false,
                'children' => [
                    'depth' => '1',
                    'craft' => false,
                ]
            ]
        ];
        
        $witchSummoning = new WitchSummoning( $this->wc, $configuration, $this->wc->website );
        $witches        = $witchSummoning->summon();
        
        $daughtersIdList = [];
        foreach( $witches['self']->daughters as $witch )
        {
            $daughtersIdList[] = $witch->id;
            
            if( empty($this->daughters[ $witch->id ]) && $this->isMotherOf($witch) ){
                $this->addDaughter($witch);
            }
        }
        
        foreach( array_keys($this->daughters) as $daughterId ){
            if( !in_array($daughterId, $daughtersIdList) ){
                $this->removeDaughter( $daughterId );
            }
        }
        
        return $this->daughters;
    }
    
    function addDaughter( self $daughter )
    {
        $this->daughters[ $daughter->id ]   = $daughter;
        $daughter->mother                   = $this;
        
        return $this->reorderDaughters();
    }
    
    function reorderDaughters()
    {
        $daughters                  = $this->daughters;
        $this->daughters            = $this->reorderWitches( $daughters );
        
        return $this;
    }
    
    function removeDaughter( self $daughter )
    {
        if( !empty($this->daughters[ $daughter->id ]) ){
            unset($this->daughters[ $daughter->id ]);
        }
        
        if( $daughter->mother->id == $this->id ){
            $daughter->mother = NULL;
        }
        
        return $this;
    }
    
    function listDaughtersIds()
    {
        $list = [];
        if( !empty($this->daughters) ){
            $list = array_keys($this->daughters);
        }
        
        return $list;
    }
    
    
    function isMotherOf( self $potentialDaughter )
    {
        if( $potentialDaughter->depth != $this->depth + 1 ){
            return false;
        }
        
        $isDaughter = true;
        
        for( $i=1; $i<=$this->depth; $i++ ){
            if( $this->{'level_'.$i} != $potentialDaughter->{'level_'.$i} )
            {
                $isDaughter = false;
                break;
            }
        }
        
        return $isDaughter;
    }
    
    function hasTarget(){
        return( !empty($this->target_table) && !empty($this->target_fk) );
    }
    
    function invoke( $assignedModuleName=false, $fromUnassigned=false )
    {
        if( !empty($assignedModuleName) ){
            $moduleName = $assignedModuleName;
        }
        else {
            $moduleName = $this->invoke;
        }
        
        if( empty($moduleName) ){
            return $this;
        }
        
        $module     = new Module( $this, $moduleName );
        $permission = $this->isAllowed( $module );
        
        if( !$permission && !empty($module->config['notAllowedRedirect']) )
        {
            $moduleName = $module->config['notAllowedRedirect'];
            $this->wc->debug->dump( "Access denied to ".$module->name." for user: ".$this->wc->user->name.", redirecting to ".$moduleName );
            return $this->invoke( $moduleName, (!$assignedModuleName || $fromUnassigned) );
        }
        elseif( !$permission )
        {
            $moduleName = '403';
            $this->wc->debug->dump( "Access denied to ".$module->name." for user: ".$this->wc->user->name.". " );
            $this->wc->user->loginMessages[] = "You must login to access this module";
            return $this->invoke( $moduleName, (!$assignedModuleName || $fromUnassigned) );
        }
        
        $this->modules[ $moduleName ]   = $module;
                
        if( !$assignedModuleName || $fromUnassigned )
        {
            $this->module = $module;
            
            // TODO set forced context from witch
            //$this->context = $this->wc->website->context;
        }
        
        $result = $module->execute();
        
        if( !$assignedModuleName || $fromUnassigned ){
            $this->result = $result;
        }
        
        return $result;
    }
    
    
    function craft( array $data=null, TargetStructure $structure=null )
    {
        if( empty($data) )
        {
            // Don't have to get or cannot get target
            if( !empty($this->target) || !$this->hasTarget() ){
                return $this;
            }
            
            if( empty($structure) ){
                $structure = new TargetStructure( $this->wc, $this->target_table );
            }
            
            // TODO GET DATA
            // $this->target = new Target( $this->wc, $structure, $data );
        }
        else 
        {
            // Can't resolve structure
            if( empty($structure) && empty($this->target_table) ){
                return $this;
            }
            elseif( empty($structure) ){
                $structure = new TargetStructure( $this->wc, $this->target_table );
            }
            
            $this->target = new Target( $this->wc, $structure, $data );
        }
        
        return $this;
    }
    
    static function getDepth( WitchCase $wc )
    {
        $cache = $wc->cache->get('system', 'depth');
        if( $cache ){
            include $cache;
        }
        
        if( empty($depth) )
        {
            $query  =   "SHOW COLUMNS FROM `witch` WHERE Field LIKE 'level_%'";
            $result =   $wc->db->selectQuery($query);
            $depth  = count($result);
            
            $wc->cache->create('system', 'depth', $depth);
        }
        
        return (int) $depth;
    }
    
    function isAllowed( Module $module=null, User $user=null )
    {
        if( empty($module) && empty($this->invoke) ){
            return false;
        }
        
        if( empty($module) ){
            $module = new Module( $this, $this->invoke );
        }
        
        if( empty($user) ){
            $user = $this->wc->user;
        }
        
        if( !empty($module->config['public']) ){
            $permission = true;
        }
        else // Is the current user has permission to access module ?
        {
            $permission = false;
            foreach( $user->policies as $policy )
            {
                if( $policy['module'] != '*' && $policy['module'] != $module->name ){
                    continue;
                }
                
                if( $policy["position"] === false )
                {
                    $permission = true;
                    break;
                }
                
                if( $policy["position_rules"]["self"] && $policy["position"] == $this->position )
                {
                    $permission = true;
                    break;
                }
                
                if( $policy["position_rules"]["ancestors"] && count($this->position) < count($policy["position"]) )
                {
                    $matchPosition = true;
                    foreach( $this->position as $level => $positionID ){
                        if( $policy["position"][ $level ] != $positionID )
                        {
                            $matchPosition = false;
                            break;
                        }
                    }
                }
                
                if( $policy["position_rules"]["descendants"] && count($policy["position"]) < count($this->position) )
                {
                    $matchPosition = true;
                    foreach( $policy["position"] as $level => $positionID ){
                        if( $this->position[ $level ] != $positionID )
                        {
                            $matchPosition = false;
                            break;
                        }
                    }
                }
                
                if( !empty($matchPosition) )
                {
                    $permission = true;
                    break;
                }
            }
        }
        
        if( !$permission ){
            $this->accessDeniedFor  = $module->name;
        }
        
        return $permission;
    }
    
    function edit( array $params )
    {
        foreach( $params as $field => $value ){
            if( !in_array($field, self::FIELDS) ){
                unset( $params[ $field ] );
            }
        }
        
        $name = $params['name'] ?? "";
        $name = trim($name);
        if( !empty($name) ){
            $params['name'] = $name;
        }
        elseif( isset($params['name']) ) {
            unset($params['name']);
        }
        
        $site   = $params['site'] ?? "";
        $site   = trim($site);
        $url    = $params['url'] ?? "";
        $url    = trim($url);
        
        if( !empty($site) && empty($url) )
        {
            $url    =   $this->findPreviousUrlForSite( $site );
            
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
        
        if( !empty($site) && !empty($url) )
        {
            $params['site'] = $site;
            $params['url']  = $this->checkUrl( $site, $url );
        }
        elseif( (isset( $params['site'] ) && empty( $params['site'] ))
                || (isset( $params['url'] ) && empty( $params['url'] ))
        ){
            $params['site'] = 'NULL';
            $params['url']  = 'NULL';
        }
        
        if( empty($params) ){
            return false;
        }
        
        $query = "";
        $query  .=  "UPDATE `witch` ";
        
        $separator = "SET ";
        foreach( $params as $field => $value )
        {
            if( $value == 'NULL' ){
                $query      .=  $separator.$this->wc->db->escape_string($field)."= NULL ";
            }
            else {
                $query      .=  $separator.$this->wc->db->escape_string($field)."='".$this->wc->db->escape_string($value)."' ";
            }
            $separator  =  ", ";
        }
        
        $query  .=  "WHERE id='".$this->id."' ";
        
        if( $this->wc->db->updateQuery($query) )
        {
            foreach( $params as $field => $value ){
                $this->{$field} = $value;
            }
            
            if( isset($params['status']) )
            {
                $this->statusLevel = $this->status;
                $this->status      = $this->wc->configuration->read('global', "status")[ $this->status ];
            }
            
            return true;
        }
        
        return false;
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
    
    function findPreviousUrlForSite( $site )
    {
        $url    = "";
        // TODO function getMother()
        $mother = $this->mother;
        while( !empty($mother) )
        {
            if( $mother->site == $site ){
                $url = $mother->url;
                break;
            }
            
            $mother = $mother->mother;
        }
        
        if( !empty($url) ){
            return $url;
        }
        
        $configuration = [
            'urlSearch' => [
                'id'    => $this->id,
                'craft' => false,
                'parents' => [
                    'depth' => '*',
                    'craft' => false,
                ]
            ]
        ];
        
        $witchSummoning = new WitchSummoning( $this->wc, $configuration, $this->wc->website );
        $witchSummoning->sitesRestrictions = [ $site, $this->site ];
        
        $witches = $witchSummoning->summon();
        
        if( empty($witches['urlSearch']) ){
            return "";
        }
        
        $mother = $witches['urlSearch']->mother;
        while( !empty($mother) )
        {
            if( $mother->site == $site ){
                $url = $mother->url;
                break;
            }
            
            $mother = $mother->mother;
        }
        
        return $url;
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
    
    function isParent( self $potentialDescendant )
    {
        $potentialDescendantPasition = $potentialDescendant->position;
        foreach( $this->position as $level => $levelPosition ){
            if( empty($potentialDescendantPasition[ $level ]) ||  $potentialDescendantPasition[ $level ] != $levelPosition ){
                return false;
            }
        }
        
        return true;
    }
}
