<?php
namespace WC;

use WC\DataAccess\Witch as WitchDA;
use WC\Datatype\ExtendedDateTime;
use WC\Structure;

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
        "craft_table",
        "craft_fk",
        "ext_id",
        "is_main",
        "context",
        "datetime",
        "priority",
    ];
    private $properties     = [];
    
    var $id;
    var $name;
    var $datetime;
    
    var $statusLevel        = 0;
    var $status;
    
    var $depth              = 0;
    var $position           = [];
    var $modules            = [];
    
    var $mother;
    var $sisters;
    var $daughters;
    
    /** @var WitchCase */
    var $wc;
    
    /**
     * This contructor should not be directly used
     * @param WitchCase $wc
     */
    function __construct( WitchCase $wc )
    {
        $this->wc = $wc;
        
        foreach( self::FIELDS as $field ){
            $this->properties[$field] = NULL;
        }
    }
    
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
     * Witch factory class, reads witch data associated whith id
     * @param WitchCase $wc
     * @param int $id   witch id to create
     * @return mixed implemented Witch obeject, boolean false if data not found
     */
    static function createFromId( WitchCase $wc, int $id ): mixed
    {
        $data = WitchDA::readFromId($wc, $id);
        
        if( empty($data) ){
            return false;
        }
        
        return self::createFromData( $wc, $data );
    }
    
    /**
     * Witch factory class, implements witch whith data provided
     * @param WitchCase $wc
     * @param array $data
     * @return self
     */
    static function createFromData(  WitchCase $wc, array $data ): self
    {
        $witch = new self( $wc );
        
        $witch->properties = $data;
        
        $witch->propertiesRead();

        $witch->position    = [];
        
        $i = 1;
        while( isset($data['level_'.$i]) )
        {
            $witch->position[$i] = (int) $witch->{'level_'.$i};
            $i++;
        }
        $witch->depth       = $i - 1; 
        
        if( $witch->depth == 0 ){
            $witch->mother = false;
        }
        
        return $witch;
    }
    
    /**
     * Update Object properties based of object var "properties"
     * @return void
     */
    function propertiesRead(): void
    {
        if( !empty($this->properties['id']) ){
            $this->id = (int) $this->properties['id'];
        }
        
        if( !empty($this->properties['name']) ){
            $this->name = $this->properties['name'];
        }
        
        if( !empty($this->properties['datetime']) ){
            $this->datetime = new ExtendedDateTime($this->properties['datetime']);
        }
        
        if( isset($this->properties['status']) ){
            $this->statusLevel = (int) $this->properties['status'];
        }
        
        $this->status      = $this->wc->configuration->read('global', "status")[ $this->statusLevel ];
        
        return;
    }
    
    /**
     * Determine if the witch is associated with a craft (ie a content)
     * @return bool
     */
    function hasCraft(): bool {
        return !empty($this->properties[ 'craft_table' ]) && !empty($this->properties[ 'craft_fk' ]);
    }
    
    /**
     * Determine if the witch is associated with a invocation (ie a module)
     * @return bool
     */
    function hasInvoke(): bool {
        return !empty($this->properties[ 'invoke' ]);
    }
    
    
    /**
     * Mother witch manipulation
     * @param self $mother
     * @return self
     */
    function setMother( self $mother ): self
    {
        $this->unsetMother();
        
        $this->mother = $mother;
        if( !in_array($this->id, array_keys($mother->daughters ?? [])) ){
            $mother->addDaughter($this);
        }
        
        return $this;
    }
    
    /**
     * Mother witch manipulation
     * @return self
     */
    function unsetMother(): self
    {
        if( !empty($this->mother) && !empty($this->mother->daughters[ $this->id ]) ){
            unset($this->mother->daughters[ $this->id ]);
        }
        
        $this->mother = null;
        
        return $this;
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
        if( is_null($this->mother) )        
        {
            $this->wc->dump( 'TODO try to locate from other witches in cairn and position', $this->name );
            
            $this->setMother( WitchDA::fetchAncestors($this->wc, $this->id, true) );
        }
        
        return $this->mother;
    }
    
    
    /**
     * Sister witches manipulation
     * @param self $sister
     * @return self
     */
    function addSister( self $sister ): self
    {
        if( empty($this->sisters) ){
            $this->sisters = [];
        }
        
        if( $sister->id != $this->id ){
            $this->sisters[ $sister->id ] = $sister;
        }
        
        return $this;
    }
    
    /**
     * Sister witches manipulation
     * @param self $sister
     * @return self
     */
    function removeSister( self $sister ): self
    {
        if( !empty($this->sisters[ $sister->id ]) ){
            unset($this->sisters[ $sister->id ]);
        }
        
        if( !empty($sister->sisters[ $this->id ]) ){
            unset($sister->sisters[ $this->id ]);
        }
        
        return $this;
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
        if( is_null($this->sisters) && $this->mother() ){
            foreach( WitchDA::fetchDescendants($this->wc, $this->mother()->id, true) as $sisterWitch ){
                $this->addSister($sisterWitch);
            }
        }
        elseif( is_null($this->sisters) ){
            $this->sisters = false;
        }
        
        if( !$id ){
            return $this->sisters;
        }
        
        return  $this->sisters[ $id ] 
                    ?? Witch::createFromData($this->wc, [ 'name' => "ABSTRACT 404 WITCH", 'invoke' => '404' ]);
    }
    
    
    /**
     * Daughter witches manipulation
     * @param self $daughter
     * @return self
     */
    function addDaughter( self $daughter ): self
    {
        $this->daughters[ $daughter->id ]   = $daughter;
        $daughter->mother                   = $this;
        
        return $this->reorderDaughters();
    }
    
    /**
     * Daughter witches manipulation
     * @return self
     */
    function reorderDaughters(): self
    {
        $daughters                  = $this->daughters;
        $this->daughters            = self::reorderWitches( $daughters );
        
        return $this;
    }
    
    /**
     * Daughter witches manipulation
     * @param self $daughter
     * @return self
     */
    function removeDaughter( self $daughter ): self
    {
        if( !empty($this->daughters[ $daughter->id ]) ){
            unset($this->daughters[ $daughter->id ]);
        }
        
        if( $daughter->mother->id == $this->id ){
            $daughter->mother = null;
        }
        
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
        if( is_null($this->daughters) )
        {
            $this->daughters = WitchDA::fetchDescendants($this->wc, $this->id, true);
            $this->reorderDaughters();
        }
        
        if( !$id ){
            return $this->daughters;
        }
        
        return  $this->daughters[ $id ] 
                    ?? Witch::createFromData($this->wc, [ 'name' => "ABSTRACT 404 WITCH", 'invoke' => '404' ]);
    }
    
    
    /**
     * Reorder a witch array based on priority
     * @param array $witchesList
     * @return array
     */
    static function reorderWitches( array $witchesList ): array
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
    
    
    /**
     * Invoke the module 
     * @param string|null $assignedModuleName
     * @param bool $isRedirection
     * @return string
     */
    function invoke( ?string $assignedModuleName=null, bool $isRedirection=false ): string
    {
        if( !empty($assignedModuleName) ){
            $moduleName = $assignedModuleName;
        }
        else {
            $moduleName = $this->properties["invoke"];
        }
        
        if( empty($moduleName) ){
            return "";
        }
        
        $module     = new Module( $this, $moduleName );
        $module->setIsRedirection( $isRedirection );
        
        if( !$module->isValid() )
        {
            $this->wc->debug->toResume( "This module is not valid", 'MODULE '.$module->name );
            $this->modules[ $moduleName ]   = false;
            return "";
        }
        
        $permission = $this->isAllowed( $module );
        
        if( !$permission && !empty($module->config['notAllowed']) )
        {
            $this->wc->debug->toResume( "Access denied to for user: ".$this->wc->user->name.", redirecting to ".$module->config['notAllowed'], 'MODULE '.$module->name  );
            $result = $this->invoke( $module->config['notAllowed'], true );
            $this->modules[ $moduleName ] = $this->modules[ $module->config['notAllowed'] ];
            return $result;
        }
        elseif( !$permission )
        {
            $this->wc->debug->toResume( "Access denied for user: ".$this->wc->user->name, 'MODULE '.$module->name );
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
     * @return boolean
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
        
//        if( !$module->getResult() ){
//            $module->execute();
//        }
        
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
        
        $name   = trim($params['name'] ?? "");
        $site   = trim($params['site'] ?? "");
        $url    = trim($params['url'] ?? "");
        
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
            $params['site'] = null;
            $params['url']  = null;
        }
        
        if( empty($params) ){
            return false;
        }
        
        
        if( !empty($name) ){
            $params['name'] = $name;
        }
        elseif( isset($params['name']) ) {
            unset($params['name']);
        }
        
        $updateResult = WitchDA::update($this->wc, $params, ['id' => $this->id]);
        
        if( $updateResult === false ){
            return false;
        }
        
        foreach( $params as $field => $value ){
            $this->properties[$field] = $value;
        }

        $this->propertiesRead();

        return $updateResult;
    }
    
    /**
     * Usefull for cleaning up url strings
     * @param string $urlRaw
     * @return string
     */
    static function urlCleanupString( string $urlRaw ): string
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

    /**
     * Usefull for string standardisation (urls, names)
     * @param string $string
     * @return string
     */
    static function cleanupString( string $string ): string
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
        
        $string0    = strtr($string, $characters);
        $string1    = preg_replace('#[^A-Za-z0-9]+#', '-', $string0);
        $string2    = trim($string1, '-');
        
        return strtolower($string2);
    }
    
    
    /**
     * Add a new witch daughter 
     * @param array $params 
     * @return mixed    
     */
    function createDaughter( array $params ): mixed
    {
        $name   = trim($params['name'] ?? "");
        if( empty($name) ){
            return false;
        }
        $site   = trim($params['site'] ?? "");
        $url    = trim($params['url'] ?? "");
        
        if( $this->depth == $this->wc->depth ){
            $this->addLevel();
        }
        
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
        $newDaughterPosition[ ($this->depth + 1) ]  = WitchDA::getNewDaughterIndex($this->wc, $this->position);
        
        $params['name'] = $name;
        $params['site'] = !empty($site)? $site: null;
        $params['url']  = !empty($url)? $url: null;
        
        foreach( $newDaughterPosition as $level => $levelPosition ){
            $params[ "level_".$level ] = $levelPosition;
        }
        
        return WitchDA::create($this->wc, $params);
    }
    
    /**
     * If with creation is at the top leaf of matriarcal arborescence,
     * Add a new level to witches genealogical tree
     * @return bool
     */
    function addLevel(): bool
    {
        $depth = WitchDA::increasePlateformDepth($this->wc);
        if( $depth == $this->wc->depth ){
            return false;
        }
        
        $this->wc->depth = $depth;
        
        return true;
    }
    
    
    /**
     * On automatic creation of new url, find the last logical parent url
     * @param string $site
     * @return string
     */
    function findPreviousUrlForSite( string $site ): string
    {
        $url    = "";
        $mother = $this->mother;
        if( is_null($mother) )
        {
            $this->setMother( WitchDA::fetchAncestors($this->wc, $this->id, true, [ $site, $this->site ]) );
            $mother = $this->mother ?? false;
        }
        
        while( $mother !== false && $mother->depth > 0 )
        {
            if( $mother->site == $site ){
                $url = $mother->url;
                break;
            }
            
            $mother = $mother->mother;
        }
        
        return $url;
    }
    
    
    /**
     * Check new url validity, add a suffix if it's not
     * @param string $site
     * @param string $url
     * @return string
     */
    function checkUrl( string $site, string $url ): string
    {
        $result = WitchDA::getUrlData($this->wc, $site, $url, (int) $this->id);
        
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
                $indice = (int) substr($row['url'], (1 + strrpos($row['url'], '-') ) );
                
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
    
    
    /**
     * Delete witch if it's not the root,
     * Delete all descendants and their associated craft if this is their only witch association
     * @param bool $fetchDescendants
     * @return bool
     */
    function delete( bool $fetchDescendants=true ): bool
    {
        if( $this->mother() === false || $this->depth == 0 ){
            return false;
        }
        
        if( $fetchDescendants ){
            foreach( WitchDA::fetchDescendants($this->wc, $this->id, true) as $daughter ){
                $this->addDaughter( $daughter );
            }
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
        
        return WitchDA::delete($this->wc, $deleteIds);
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
        
        return $this->edit([ 'craft_table' => $structure->table, 'craft_fk' => $craftId ]);
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
     * @return mixed
     */
    function getUrl( ?array $queryParams=null, ?Website $forcedWebsite=null ): mixed
    {
        $website = $forcedWebsite ?? $this->wc->website;
        
        if( $this->site !== $website->site || !$this->url ){
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
    
    static function recursiveTree( self $witch, $sitesRestrictions=false, $currentId=false, $maxStatus=false, ?array $hrefCallBack=null )
    {
        if( !is_null($witch->site) 
            && $sitesRestrictions !== false
            && !in_array($witch->site, $sitesRestrictions) ){
            return false;
        }

        $path       = false;
        if( $currentId && $currentId == $witch->id ){
            $path = true;
        }

        $daughters  = [];
        foreach( $witch->daughters() as $daughterWitch )
        {
            if( $maxStatus !== false && $daughterWitch->statusLevel > $maxStatus ){
                continue;
            }

            $subTree        = self::recursiveTree( $daughterWitch, $sitesRestrictions, $currentId, $maxStatus, $hrefCallBack );
            if( $subTree === false ){
                continue;
            }

            if( $subTree['path'] ){
                $path = true;
            }

            $daughters[ $subTree['id'] ]    = $subTree;
        }

        $tree   = [ 
            'id'                => $witch->id,
            'name'              => $witch->name,
            'site'              => $witch->site ?? "",
            'description'       => $witch->data,
            'craft'             => $witch->hasCraft(),
            'invoke'            => $witch->hasInvoke(),
            'daughters'         => $daughters,
            'daughters_orders'  => array_keys( $daughters ),
            'path'              => $path,
        ];
        
        if( $hrefCallBack ){
            $tree['href'] = call_user_func( $hrefCallBack, $witch );
        }
        
        return $tree;
    }
}
