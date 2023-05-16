<?php
namespace WC;

use WC\DataAccess\WitchCrafting;
use WC\DataAccess\Witch as WitchDA;
use WC\Datatype\ExtendedDateTime;

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
    
    var $id;
    var $name;
    var $datetime;
    
    var $statusLevel        = 0;
    var $status;
    
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
        $data = WitchDA::readFromId($wc, $id);
        
        if( empty($data) ){
            return false;
        }
        
        return self::createFromData( $wc, $data );
    }
    
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
    
    function propertiesRead()
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
    
    function setMother( self $mother ): self
    {
        $this->unsetMother();
        
        $this->mother = $mother;
        if( !in_array($this->id, array_keys($mother->daughters)) ){
            $mother->addDaughter($this);
        }
        
        return $this;
    }
    
    function unsetMother(): self
    {
        if( !empty($this->mother) && !empty($this->mother->daughters[ $this->id ]) ){
            unset($this->mother->daughters[ $this->id ]);
        }
        
        $this->mother = NULL;
        
        return $this;
    }
    
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
    
    function listSistersIds(): array
    {
        $list = [];
        if( !empty($this->sisters) ){
            $list = array_keys($this->sisters);
        }
        
        return $list;
    }
    
    function reorderWitches( array $witchesList ): array
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
    
    function addDaughter( self $daughter ): self
    {
        $this->daughters[ $daughter->id ]   = $daughter;
        $daughter->mother                   = $this;
        
        return $this->reorderDaughters();
    }
    
    function reorderDaughters(): self
    {
        $daughters                  = $this->daughters;
        $this->daughters            = $this->reorderWitches( $daughters );
        
        return $this;
    }
    
    function removeDaughter( self $daughter ): self
    {
        if( !empty($this->daughters[ $daughter->id ]) ){
            unset($this->daughters[ $daughter->id ]);
        }
        
        if( $daughter->mother->id == $this->id ){
            $daughter->mother = NULL;
        }
        
        return $this;
    }
    
    function listDaughtersIds(): array
    {
        $list = [];
        if( !empty($this->daughters) ){
            $list = array_keys($this->daughters);
        }
        
        return $list;
    }
    
    
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
    
    function hasTarget(){
        return !empty($this->properties[ 'target_table' ]) && !empty($this->properties[ 'target_fk' ]);
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
    
    function craft()
    {
        if( !$this->hasTarget() ){
            return false;
        }
        
        return $this->wc->cairn->craft( $this->target_table, $this->target_fk );
    }
        
    function isAllowed( Module $module=null, User $user=null ): bool
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
    
    function edit( array $params ): bool
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
        
        if( WitchDA::update($this->wc, $params, ['id' => $this->id]) )
        {
            foreach( $params as $field => $value ){
                $this->properties[$field] = $value;
            }
            
            $this->propertiesRead();
            
            return true;
        }
        
        return false;
    }
    
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
    
    
    function createDaughter( array $params ): bool
    {
        $name   = trim($params['name'] ?? "");
        if( empty($name) ){
            return false;
        }
        $site   = trim($params['site'] ?? "");
        $url    = trim($params['url'] ?? "");
        
        if( $this->depth == $this->wc->website->depth ){
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
    
    function addLevel(): bool
    {
        $depth = WitchDA::increasePlateformDepth($this->wc);
        if( $depth == $this->wc->website->depth ){
            return false;
        }
        
        $this->wc->website->depth = $depth;
        
        return true;
    }
        
    function findPreviousUrlForSite( string $site ): string
    {
        $url    = "";
        $mother = $this->mother;
        while( $mother !== false && $mother->depth > 0 )
        {
            if( is_null($mother) )
            {
                $this->setMother( WitchDA::fetchAncestors($this->wc, $this->id, true, [ $site, $this->site ]) );
                $mother = $this->mother ?? false;
            }
            
            if( $mother->site == $site ){
                $url = $mother->url;
                break;
            }
            
            $mother = $mother->mother;
        }
                
        return $url;
    }
    
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
    
    function delete( bool $fetchDescendants=true ): bool
    {
        if( $this->mother === false || $this->depth == 0 ){
            return false;
        }
        
        if( $fetchDescendants ){
            foreach( WitchDA::fetchDescendants($this->wc, $this->id, true, false) as $daughter ){
                $this->addDaughter( $daughter );
            }
        }
        
        $deleteIds = array_keys($this->daughters);
        foreach( $this->daughters as $daughter ){
            if( !$daughter->delete(false) ){
                return false;
            }
        }
        
        $this->removeTarget();
        if( $fetchDescendants ){
            $deleteIds[] = $this->id;
        }
        
        return WitchDA::delete($this->wc, $deleteIds);
    }    
    
    function removeTarget(): bool
    {
        if( !$this->hasTarget() ){
            return false;
        }
        
        if( $this->craft()->countWitches() == 1 ){
            $this->craft()->delete();
        }
        
        return $this->edit(['target_table' => null, 'target_fk' => null]);
    }
    
    function addTargetStructure( TargetStructure $targetStructure ): bool
    {
        $targetId = $targetStructure->createTarget( $this->name );
        
        if( empty($targetId) ){
            return false;
        }
        
        if( $this->hasTarget() && $this->craft()->countWitches() == 1 ){
            $this->craft()->delete();
        }
        
        return $this->edit([ 'target_table' => $targetStructure->table, 'target_fk' => $targetId ]);
    }
    
    function addTarget( Target $target ): bool
    {
        if( $this->hasTarget() && $this->craft()->countWitches() == 1 ){
            $this->craft()->delete();
        }
        
        $this->wc->cairn->setCraft($target, $target->structure->table, $target->id);
        
        return $this->edit([ 'target_table' => $target->structure->table, 'target_fk' => $target->id ]);
    }
    
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
}
