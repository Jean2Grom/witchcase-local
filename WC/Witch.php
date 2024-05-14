<?php
namespace WC;

use WC\Handler\WitchHandler as Handler;
use WC\DataAccess\Witch as DataAccess;
use WC\Structure;

use WC\Handler\CauldronHandler;

/**
 * A witch is an element of global arborescence, that we call matriarcat. 
 * Each witch except root (ID 1) has a mother witch, and can have daughters witch
 * The structure tree of WC is composed of witches, witch represents the elements of it
 * Each Witch car be associated with a craft, a module and an URL to execute, 
 * a visibility status ... 
 * This class is very essential in the WC management
 *
 * @author jean2Grom
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
        "cauldron",
        "craft_table",
        "craft_fk",
        "is_main",
        "context",
        "datetime",
        "priority",
    ];
    public $properties     = [];
    
    public $id;
    public $name;
    public $datetime;
    
    public $statusLevel     = 0;
    public $status;
    
    public $site;
    
    public $depth           = 0;
    public $position        = [];
    public $modules         = [];
    
    public $mother;
    public $sisters;
    public $daughters;

    public $cauldronId;
    
    /**
     * @var ?Cauldron
     */
    public ?Cauldron $cauldron;
    
    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;
    
    
    /**
     * Property setting 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void {
        $this->properties[$name] = $value;
    }
    

    /**
     * Property reading
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed {
        return $this->properties[$name] ?? null;
    }
    

    /**
     * Name reading
     * @return string
     */
    public function __toString(): string {
        return ($this->id)? $this->name: "";
    }
    

    /**
     * Is this witch exist in database ?
     * @return bool
     */
    function exist(): bool {
        return !empty($this->id);
    }
    
    
    /**
     * Resolve status label with Website generation
     * @return ?string status label
     */
    function status()
    {
        if( $this->status ){
            return $this->status;
        }
        
        if( $this->site ){
            $statusList = (new Website( $this->wc, $this->site ))->status;
        }
        
        if( empty($statusList) ){
            $statusList = $this->wc->configuration->read('global', "status");
        }
        
        if( isset($statusList[ $this->statusLevel ]) ){
            $this->status = $statusList[ $this->statusLevel ];
        }
        
        return $this->status;
    }
    
    /**
     * Determine if the witch is associated with a craft (ie a content)
     * @return bool
     */
    function hasCraft(): bool {
        return !empty($this->properties[ 'craft_table' ]) && !empty($this->properties[ 'craft_fk' ]);
    }
    
    /**
     * Determine if the witch is associated with a craft (ie a content)
     * @return bool
     */
    function hasCauldron(): bool {
        return !empty($this->properties[ 'cauldron' ]);
    }
    
    /**
     * Determine if the witch is associated with a invocation (ie a module)
     * @return bool
     */
    function hasInvoke(): bool {
        return !empty($this->properties[ 'invoke' ]);
    }
    
        
    /**
     * Mother witch test
     * @param self $potentialDaughter
     * @return bool
     */
    function isMotherOf( self $potentialDaughter ): bool
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
    
    /**
     * Read mother witch (get it if needed), 
     * return mother witch or false if witch is root
     * @return mixed
     */
    function mother(): mixed
    {
        if( is_null($this->id) ){
            return false;
        }
        
        if( is_null($this->mother) )
        {
            $motherPosition = $this->position;
            unset( $motherPosition[array_key_last( $motherPosition )] );
            
            $mother = $this->wc->cairn->searchFromPosition($motherPosition);
            if( $mother ){
                Handler::setMother( $this, $mother );
            }
        }
        
        if( is_null($this->mother) ){
            Handler::setMother( $this, DataAccess::fetchAncestors($this->wc, $this->id, true) );
        }
        
        return $this->mother;
    }
    
        
    /**
     * Sister witches manipulation
     * @return array
     */
    function listSistersIds(): array
    {
        $list = [];
        if( !empty($this->sisters) ){
            $list = array_keys($this->sisters);
        }
        
        return $list;
    }
    
    /**
     * Read Sister witches (get them if needed), 
     * return mother witch or false if witch is root
     * @return mixed
     */
    function sisters( ?int $id=null ): mixed
    {
        if( is_null($this->id) ){
            return false;
        }
        
        if( is_null($this->sisters) && $this->mother() ){
            foreach( DataAccess::fetchDescendants($this->wc, $this->mother()->id, true) as $sisterWitch ){
                Handler::addSister( $this, $sisterWitch );
            }
        }
        elseif( is_null($this->sisters) ){
            $this->sisters = false;
        }
        
        if( !$id ){
            return $this->sisters;
        }
        
        return  $this->sisters[ $id ] 
                    ?? Handler::createFromData($this->wc, [ 'name' => "ABSTRACT 404 WITCH", 'invoke' => '404' ]);
    }
    
    
    /**
     * Daughter witches manipulation
     * @return self
     */
    function reorderDaughters(): self
    {
        $daughters                  = $this->daughters;
        $this->daughters            = Handler::reorderWitches( $daughters );
        
        return $this;
    }
    
    
    /**
     * Daughter witches manipulation
     * @return array
     */
    function listDaughtersIds(): array
    {
        $list = [];
        if( !empty($this->daughters) ){
            $list = array_keys($this->daughters);
        }
        
        return $list;
    }
    
    /**
     * Read Daughter witches (get them if needed), 
     * return mother witch or false if witch is root
     * @return mixed
     */
    function daughters( ?int $id=null ): mixed
    {
        if( is_null($this->id) ){
            return false;
        }
        
        if( is_null($this->daughters) ){
            Handler::addDaughters( 
                $this, 
                DataAccess::fetchDescendants($this->wc, $this->id, true) 
            );
        }
        
        if( !$id ){
            return $this->daughters;
        }
        
        return  $this->daughters[ $id ] 
                    ?? Handler::createFromData(
                        $this->wc, 
                        [ 'name' => "ABSTRACT 404 WITCH", 'invoke' => '404' ]
                    );
    }
    
    
    /**
     * Invoke the module 
     * @param string|null $assignedModuleName
     * @param bool $isRedirection
     * @return string
     */
    function invoke( ?string $assignedModuleName=null, bool $isRedirection=false, bool $allowContextSetting=false ): string
    {
        if( !empty($assignedModuleName) ){
            $moduleName = $assignedModuleName;
        }
        else 
        {
            $moduleName             = $this->properties["invoke"];
            $allowContextSetting    = true;
        }
        
        if( empty($moduleName) ){
            return "";
        }
        
        $module     = new Module( $this, $moduleName );
        $module->setIsRedirection( $isRedirection );
        $module->setAllowContextSetting( $allowContextSetting );
        
        if( !$module->isValid() )
        {
            $this->wc->debug->toResume( "This module is not valid", 'MODULE '.$module->name );
            $this->modules[ $moduleName ]   = false;
            return "";
        }
        
        $permission = $this->isAllowed( $module );
        
        if( !$permission && !empty($module->config['notAllowed']) )
        {
            $this->wc->debug->toResume( "Access denied to for user: \"".$this->wc->user->name."\", redirecting to \"".$module->config['notAllowed']."\"", 'MODULE '.$module->name  );
            $result = $this->invoke( $module->config['notAllowed'], true, $allowContextSetting );
            $this->modules[ $moduleName ] = $this->modules[ $module->config['notAllowed'] ];
            return $result;
        }
        elseif( !$permission )
        {
            $this->wc->debug->toResume( "Access denied for user: \"".$this->wc->user->name."\"", 'MODULE '.$module->name );
            $this->modules[ $moduleName ]   = false;
            return "";
        }
        
        $this->modules[ $moduleName ]   = $module;
        
        return $module->execute();
    }    
    
    /**
     * Is user allowed to execute the module
     * @param Module $module
     * @param User $user
     * @return bool
     */
    function isAllowed( Module $module, User $user=null ): bool
    {
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
        
        return $permission;
    }
    
    /**
     * Read witch module, invoke it if needed
     * @param string|null $invoke
     * @return Module|boolean
     */
    function module( ?string $invoke=null )
    {
        $moduleInvoked  = $invoke ?? $this->properties["invoke"];
        
        if( !$moduleInvoked )
        {
            $this->wc->debug( "Try to reach unnamed module");
            return false;
        }        
        
        if( !isset($this->modules[ $moduleInvoked ]) ){
            $this->invoke( $moduleInvoked );
        }
        
        return $this->modules[ $moduleInvoked ];
    }
    

    /**
     * Read witch module result, invoke it if needed
     * @param string|null $invoke
     * @return string
     */
    function result( ?string $invoke=null )
    {
        $module = $this->module( $invoke );
        
        if( !$module ){
            return "";
        }
        
        return $module->getResult() ?? "";
    }
    

    /**
     * Craft witch content, store it in the Cairn (if exists, only read it)
     * @return mixed
     */
    function craft()
    {
        if( !$this->hasCraft() ){
            return false;
        }
        
        return $this->wc->cairn->craft( $this->craft_table, $this->craft_fk );
    }
    
    
    /**
     * Generate Craft witch structure
     * @return mixed
     */
    function getCraftStructure()
    {
        if( !$this->hasCraft() ){
            return false;
        }
        
        return new Structure( $this->wc, $this->craft_table );
    }
    
    
    /**
     * Update Witch
     * @param array $params
     * @return mixed
     */
    function edit( array $params ): mixed
    {
        foreach( $params as $field => $value ){
            if( !in_array($field, self::FIELDS) ){
                unset($params[ $field ]);
            }
        }
        
        // Name cannot be set to empty string
        if( !empty($params['name']) ){
            $params['name']   = trim($params['name']);
        }
        
        if( empty($params['name']) ){
            unset($params['name']);
        }
        
        $paramsKeyArray = array_keys($params);
        
        // If invoke is set to null
        if( in_array('invoke', $paramsKeyArray) && is_null($params['invoke']) )
        {
            //$params['site'] = null;
            $params['url']  = null;
        }
        // If invoke is not set but is actually null
        elseif( !in_array('invoke', $paramsKeyArray) && is_null($this->properties['invoke']) )
        {
            //$params['site'] = null;
            $params['url']  = null;
        }
        // If site is set to null
        elseif( in_array('site', $paramsKeyArray) && empty($params['site']) )
        {
            $params['site']     = null;
            $params['url']      = null;
            $params['invoke']   = null;
        }
        // If site is not set but is actually null
        elseif( !in_array('site', $paramsKeyArray) && is_null($this->properties['site']) )
        {
            $params['site']     = null;
            $params['url']      = null;
            $params['invoke']   = null;
        }
        // Invoke and site are valid and URL update is required
        elseif( in_array('url', $paramsKeyArray) )
        {
            $site       = $params['site'] ?? $this->properties['site'];
            $urlArray   = [];
            
            // If url is set to a value (ie not null)
            if( !is_null($params['url']) ){
                $urlArray[] = Handler::urlCleanupString( $params['url'] );
            }
            else 
            {
                $rootUrl    = ""; 
                if( $this->mother() ){
                    $rootUrl    = $this->mother()->getClosestUrl( $site );
                }
                
                if( !empty($rootUrl) ){
                    $rootUrl .= '/';
                }
                else {
                    $urlArray[] = '';
                }
                
                $rootUrl    .=  Handler::cleanupString($params['name'] ?? $this->name);
                $urlArray[] =   $rootUrl;                
            }
            
            if( !empty($urlArray) ){
                $params['url'] = Handler::checkUrls( $this->wc, $site, $urlArray, $this->id );
            }
        }
        
        if( empty($params) ){
            return false;
        }
        
        $updateResult = DataAccess::update($this->wc, $params, ['id' => $this->id]);
        
        if( $updateResult === false ){
            return false;
        }
        
        foreach( $params as $field => $value ){
            $this->properties[$field] = $value;
        }

        Handler::readProperties( $this );

        return $updateResult;
    }
    
    /**
     * Add a new witch daughter 
     * @param array $params 
     * @return mixed    
     */
    function createDaughter( array $params ): mixed
    {
        // Name cannot be set to empty string 
        $params['name'] = trim( $params['name'] ?? "" );
        
        if( empty($params['name']) ){
            return false;
        }
        
        if( $this->depth == $this->wc->depth ){
            Handler::addLevel($this->wc);
        }
        
        $newDaughterPosition                        = $this->position;
        $newDaughterPosition[ ($this->depth + 1) ]  = DataAccess::getNewDaughterIndex($this->wc, $this->position);
        
        if( !isset($params['site']) ){
            $params['site'] = $this->site;
        }
        
        /*
        if( !isset($params['status']) ){
            $params['status'] = $this->statusLevel;
        }
        */
        
        if( empty($params['invoke']) || empty($params['site']) || !in_array('url', array_keys($params)) )
        {
            $params['url']      = null;
            $params['invoke']   = null;
        }
        else
        {
            $urlArray   = [];
            
            // If url is set to a value (ie not null)
            if( !is_null($params['url']) ){
                $urlArray[] = Handler::urlCleanupString( $params['url'] );
            }
            else 
            {
                $rootUrl    = ""; 
                if( $this->mother() ){
                    $rootUrl    = $this->mother()->getClosestUrl( $params['site'] );
                }
                
                if( !empty($rootUrl) ){
                    $rootUrl .= '/';
                }
                else {
                    $urlArray[] = '';
                }
                
                $rootUrl    .=  Handler::cleanupString($params['name']);
                $urlArray[] =   $rootUrl;                
            }
            
            if( !empty($urlArray) ){
                $params['url'] = Handler::checkUrls( $this->wc, $params['site'], $urlArray, $this->id );
            }
        }        
        
        foreach( $newDaughterPosition as $level => $levelPosition ){
            $params[ "level_".$level ] = $levelPosition;
        }
        
        return DataAccess::create($this->wc, $params);
    }
        
    
    /**
     * Get closest ancestor url for a given site
     * @param string|null $forSite
     * @return string
     */
    function getClosestUrl( ?string $forSite=null ): string
    {
        $url    = "";
        $site   = $forSite ?? $this->site;
        
        $ancestorWitch = $this;
        while( $ancestorWitch !== false && $ancestorWitch->depth > 0 )
        {
            if( $ancestorWitch->site == $site ){
                $url = $ancestorWitch->url ?? "";
                break;
            }
            
            $ancestorWitch = $ancestorWitch->mother();
        }
        
        return $url;
    }
    
    
    /**
     * Delete witch if it's not the root,
     * Delete all descendants and their associated craft if this is their only witch association
     * @param bool $fetchDescendants
     * @return bool
     */
    function delete( bool $fetchDescendants=true ): bool
    {
        if( is_null($this->id) || $this->mother() === false || $this->depth == 0 ){
            return false;
        }
        
        if( $fetchDescendants ){
            Handler::addDaughters( 
                $this, 
                DataAccess::fetchDescendants($this->wc, $this->id, true) 
            );
        }
        
        $deleteIds = array_keys($this->daughters ?? []);
        foreach( $this->daughters ?? [] as $daughter ){
            if( !$daughter->delete(false) ){
                return false;
            }
        }
        
        $this->removeCraft();
        if( $fetchDescendants ){
            $deleteIds[] = $this->id;
        }
        
        return DataAccess::delete($this->wc, $deleteIds);
    }    
    
    /**
     * Delete associated craft if this is their only witch association,
     * if not only remove this association
     * @return bool
     */
    function removeCraft(): bool
    {
        if( !$this->hasCraft() ){
            return false;
        }
        
        $countCraftWitch = $this->craft()->countWitches();
        
        if( $countCraftWitch == 1 ){
            $this->craft()->delete();
        }
        elseif( $this->properties['is_main'] == 1 && $countCraftWitch > 1  ){
            foreach( $this->craft()->getWitches() as $id => $craftWitch ){
                if( $id != $this->id )
                {
                    $craftWitch->edit([ 'is_main' => 1 ]);
                    break;
                }
            }
        }
        
        return $this->edit(['craft_table' => null, 'craft_fk' => null]);
    }
    
    /**
     * Add new craft from past structure
     * @param Structure $structure
     * @return bool
     */
    function addStructure( Structure $structure ): bool
    {
        $craftId = $structure->createCraft( $this->name );
        
        if( empty($craftId) ){
            return false;
        }
        
        if( $this->hasCraft() && $this->craft()->countWitches() == 1 ){
            $this->craft()->delete();
        }
        
        return $this->edit([ 'craft_table' => $structure->table, 'craft_fk' => $craftId, 'is_main' => 1 ]);
    }
    
    /**
     * Add craft
     * @param Craft $craft
     * @return bool
     */
    function addCraft( Craft $craft ): bool
    {
        if( $this->hasCraft() && $this->craft()->countWitches() == 1 ){
            $this->craft()->delete();
        }
        
        $this->wc->cairn->setCraft($craft, $craft->structure->table, $craft->id);
        
        return $this->edit([ 'craft_table' => $craft->structure->table, 'craft_fk' => $craft->id ]);
    }
        
    /**
     * Test if witch is a descendant
     * @param self $potentialDescendant
     * @return bool
     */
    function isParent( self $potentialDescendant ): bool
    {
        $potentialDescendantPasition = $potentialDescendant->position;
        foreach( $this->position as $level => $levelPosition ){
            if( empty($potentialDescendantPasition[ $level ]) ||  $potentialDescendantPasition[ $level ] != $levelPosition ){
                return false;
            }
        }
        
        return true;
    }
    
    
    /**
     * Generate this witch url, 
     * relative if no forcedWebsite is passed, full if there is one
     * @param array|null $queryParams
     * @param Website|null $forcedWebsite
     */
    function url( ?array $queryParams=null, ?Website $forcedWebsite=null )
    {
        $website = $forcedWebsite ?? $this->wc->website;
        
        if( $this->site !== $website->site || is_null($this->url) ){
            return null;
        }
        
        if( $forcedWebsite ){            
            $method = "getFullUrl";
        }
        else {
            $method = "getUrl";   
        }
        
        $queryString    = '';
        $separator      = '?';
        if( $queryParams ){
            foreach( $queryParams as $paramKey => $paramValue )
            {
                if( $paramKey != '#' )
                {
                    $queryString    .=  $separator;
                    $separator      =   '&';
                }
                
                $queryString    .=  $paramKey.'='.$paramValue;
            }
        }
        
        return call_user_func([$website, $method], $this->url.$queryString);
    }
    
    
    function moveTo( self $witch )
    {
        $this->wc->db->begin();
        try {
            $this->innerTransactionMoveTo( $witch );
        } 
        catch( \Exception $e ) 
        {
            $this->wc->log->error($e->getMessage());
            $this->wc->db->rollback();
            return false;
        }
        $this->wc->db->commit();
        
        return true;        
    }
    
    private function innerTransactionMoveTo( self $witch, array $urlSiteRewrite=[] )
    {
        $position = $witch->position;
        
        $depth = count($position);
        
        if( $depth == $this->wc->depth + 1 ){
            Handler::addLevel($this->wc);
        }
        
        $newPosition                    = $position;
        $newPosition[ ($depth + 1) ]    = DataAccess::getNewDaughterIndex($this->wc, $position);
        
        $params = [];
        for( $i=1; $i <= $this->wc->depth; $i++ ){
            $params[ "level_".$i ] = NULL;
        }
        
        foreach( $newPosition as $level => $levelPosition ){
            $params[ "level_".$level ] = $levelPosition;
        }

        if( $this->mother() && !empty($this->properties['url']) && !empty($this->properties['site']) )
        {
            $previousUrl = $this->mother()->getClosestUrl();
            if( str_starts_with($this->url, $previousUrl) )
            {
                $url            = substr( $this->url, strlen($previousUrl) );
                $destinationUrl = $urlSiteRewrite[ $this->site ] ?? $witch->getClosestUrl( $this->site );
                $params['url']  = $destinationUrl.$url;
                if( substr($params['url'], 0, 1) === '/' && substr($params['url'], 1, 1) === '/' ){
                    $params['url']  = substr($params['url'], 1);
                }
                $urlSiteRewrite[ $this->site ] = $params['url'];
            }
        }
        
        $daughters      = $this->daughters();
        DataAccess::update($this->wc, $params, ['id' => $this->id]);
        $this->position = $newPosition;
        $this->depth    = count( $this->position );

        if( !empty($daughters) ){
            foreach( $daughters as $daughterWitch )
            {
                $daughterWitch->innerTransactionMoveTo( $this, $urlSiteRewrite );
            }
        }
        
        return;
    }

    function copyTo( self $witch )
    {
        $this->wc->db->begin();
        try {
            $this->innerTransactionCopyTo( $witch );
        } 
        catch( \Exception $e ) 
        {
            $this->wc->log->error($e->getMessage());
            $this->wc->db->rollback();
            return false;
        }
        $this->wc->db->commit();
        
        return true;        
    }

    private function innerTransactionCopyTo( self $witch, array $urlSiteRewrite=[] )
    {
        $params = [
            "name"          => $this->name,
            "data"          => $this->data,
            "status"        => $this->statusLevel,
            "priority"      => $this->priority,
            "craft_table"   => $this->craft_table,
            "craft_fk"      => $this->craft_fk,
            "is_main"       => 0,
            "site"          => $this->site,
            "url"           => $this->url,
            "invoke"        => $this->invoke,
            "context"       => $this->context,
        ];
        
        if( $this->mother() && !empty($params['url']) && !empty($params['site']) )
        {
            $previousUrl = $this->mother()->getClosestUrl();
            if( str_starts_with($params['url'], $previousUrl) )
            {
                $url            = substr( $params['url'], strlen($previousUrl) );
                $destinationUrl = $urlSiteRewrite[ $this->site ] ?? $witch->getClosestUrl( $this->site );
                $params['url']  = $destinationUrl.$url;
                $urlSiteRewrite[ $this->site ] = $params['url'];
            }
        }
        
        $newWitch   = Handler::createFromId($this->wc, $witch->createDaughter( $params ));        
        $daughters  = $this->daughters();
        
        if( !empty($daughters) ){
            foreach( $daughters as $daughterWitch )
            {
                $daughterWitch->innerTransactionCopyTo( $newWitch, $urlSiteRewrite );
            }
        }
        
        return;
    }
    
    /**
     * Craft witch content, store it in the Cairn (if exists, only read it)
     * @return ?Cauldron
     */
    function cauldron(): ?Cauldron
    {
        if( !$this->hasCauldron() ){
            return null;
        }

        if( empty($this->cauldron) )
        {
            $result             =   CauldronHandler::fetch(
                                        $this->wc, 
                                        [ $this->properties["cauldron"] ]
                                    );
            $this->cauldron     =   $result[ $this->properties[ "cauldron" ] ] ?? null;
            $this->cauldronId   =   $this->cauldron?->id;
        }
        
        return $this->cauldron;
    }
    


}
