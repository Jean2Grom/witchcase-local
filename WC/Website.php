<?php
namespace WC;

use WC\DataAccess\WitchSummoning;

/**
 * Description of Website
 *
 * @author teletravail
 */
class Website 
{
    var $name;
    var $currentAccess;
    var $access;
    var $adminForSites;
    var $sitesRestrictions;
    var $baseUri;
    var $url;
    var $modulesList;
    private $rootUrl;
    
    var $modules;
    var $attributes;
    var $status;
    var $witches;
    var $extensions;
    var $siteHeritages;
    var $depth;
    var $witchSummoning;    
    var $context;    
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc, string $name, string $siteAccess='' )
    {
        $this->wc           = $wc;
        $this->name         = $name;        
        $siteHeritages      = $this->wc->configuration->getSiteHeritage( $this->name );        
        $siteHeritages[]    = "global";
        $siteConfiguration  = [];
        
        foreach( $siteHeritages as $section ){
            foreach( $this->wc->configuration->read($section) as $param => $value ){
                if( !is_array($value) ){
                    $siteConfiguration[ $param ] = $value;
                    continue;
                }
                else 
                {
                    $previousValue                  = $siteConfiguration[ $param ] ?? [];
                    $siteConfiguration[ $param ]    = array_replace_recursive($previousValue, $value);
                }
            }
        }
        
        foreach( $siteConfiguration as $key => $value ){
            $this->{$key} = $value;
        }
        
        $this->siteHeritages        = $siteHeritages;
        $this->access               = $this->wc->configuration->read($this->name, "access");
        $this->adminForSites        = $this->wc->configuration->read($this->name, "adminForSites");        
        $this->sitesRestrictions    = [ $this->name ];
        $adminForSites              = !empty($this->adminForSites)? $this->adminForSites :[];
        
        foreach( $adminForSites as $adminisratedSite )
        {
            if( $adminisratedSite == '*' )
            {
                $this->sitesRestrictions = false;
                break;
            }

            $this->sitesRestrictions[] = $adminisratedSite;
        }
        
        if( empty($siteAccess) ){
            $siteAccess = array_values($this->access)[0];
        }
        
        $this->currentAccess    = $siteAccess;
        $firstSlashPosition     = strpos($this->currentAccess, '/');
        $this->baseUri          = ($firstSlashPosition !== false)? substr( $this->currentAccess, $firstSlashPosition ): '';
        $this->depth            = Witch::getDepth( $this->wc );
        
        foreach( $this->modules ?? [] as $moduleName => $moduleConf ){
            foreach( $moduleConf['witches'] ?? [] as $moduleWitchName => $moduleWitchConf ){
                if( empty($this->witches[ $moduleWitchName ]) ){
                    $this->witches[ $moduleWitchName ] = array_replace_recursive( 
                        $moduleWitchConf, 
                        [ 'module' => $moduleName ] 
                    );
                }
            }
        }
        
        $this->witchSummoning   = new WitchSummoning( $this->wc, $this->witches, $this ); 
        unset($this->witches);
        
        $this->context = new Context( $this );
    }
    
    
    
    /**
     * Determine and store the url relative to the website
     * 
     * @param string $access : uri acceded by browser request 
     * @param string $forSiteAccess : string to force siteAccess if needed
     * @return $this
     */
    function urlSetup( string $access, string $forSiteAccess='' )
    {
        if( empty($forSiteAccess) ){
            $forSiteAccess = $this->currentAccess;
        }
        
        if( strstr($access, '?') ){
            $access = strstr($access, '?', true);
        }
        
        $this->url = Witch::urlCleanupString( substr( $access, strlen($forSiteAccess) ) );
        
        return $this;
    }
    
    
    function summonWitches()
    {
        $this->witches  = $this->witchSummoning->summon();
        
        return $this;
    }
    
    
    function sabbath()
    {
        foreach( $this->witchSummoning->configuration as $refWitch => $witchConf ){
            if( !empty($this->witches[ $refWitch ]) )
            {
                if( empty($witchConf['invoke']) ){
                    continue;
                }
                
                if( is_string($witchConf['invoke']) 
                        && empty($this->witches[ $refWitch ]->modules[ $witchConf['invoke'] ]) ){
                    $this->witches[ $refWitch ]->invoke( $witchConf['invoke'] );
                }
                elseif( empty($this->witches[ $refWitch ]->result) ){
                    $this->witches[ $refWitch ]->invoke();
                }
            }
        }
        
        return true;
    }
    
    function display()
    {
        //$context = $this->context->setExecFile('default');
        
        $this->context->setExecFile('default')->display();
        
        //include $this->context->getExecFile();
        
        return $this;
    }
    
    
    function getFilePath( $filename )
    {
        // Looking in this site
        $filePath = "sites/".$this->name."/".$filename;
        
        if( is_file($filePath) ){
            return $filePath;
        }
        
        // Looking in herited sites
        foreach( $this->siteHeritages as $heritedSite )
        {
            $filePath = "sites/".$heritedSite."/".$filename;

            if( file_exists($filePath) ){
                return $filePath;
            }
        }
        
        // Looking in extension files
        /*
        $extensions = $this->wc->configuration->getExtensions($this->wc->website->name);
        if( is_array($extensions) ){
            foreach( $extensions as $extension )
            {
                $filePath = "extensions/".$extension."/".$filename;
                
                if( file_exists($filePath) ){
                    return $filePath;
                }
            }
        }
        */
        
        // Looking in default site
        $filePath = "sites/default/".$filename;
        
        if( is_file($filePath) ){
            return $filePath;
        }
        
        // Looking in system files
        $filePath = "system/".$filename;
        
        if( is_file($filePath) ){
            return $filePath;
        }
        
        return false;
    }
    
    function listModules()
    {
        if( !empty($this->modulesList) ){
            return $this->modulesList;
        }
        
        $modulesList = [];
        
        foreach( $this->siteHeritages as $siteItem )
        {
            if( $siteItem == "global" ){
                $dir = "sites/default/".Module::CONTROLLER_SUBFOLDER;
            }
            else {
                $dir = "sites/".$siteItem."/".Module::CONTROLLER_SUBFOLDER;
            }
            
            if( is_dir($dir) ){
                $modulesList = array_merge($modulesList, $this->recursiveRead( $dir, $dir ));
            }
        }
        
        $modulesList = array_unique($modulesList);
        
        sort($modulesList);
        
        $this->modulesList = $modulesList;
        
        return $this->modulesList;
    }
    
    private function recursiveRead( $dir, $prefix )
    {
        $modulesList = [];
        
        $dirContentArray = array_diff( scandir($dir), array('..', '.') );
        
        foreach( $dirContentArray as $dirContent ){
            if( is_dir($dir.'/'.$dirContent) ){
                $modulesList = array_merge( $modulesList, $this->recursiveRead( $dir.'/'.$dirContent, $prefix ) );
            }
            else 
            {
                $moduleName = $dir.'/'.$dirContent;
                if( substr($moduleName, 0, strlen($prefix)) == $prefix ){
                    $moduleName = substr($moduleName, strlen($prefix) + 1);
                }
                
                $moduleName = substr($moduleName, 0, strripos($moduleName, ".php") );
                
                $modulesList[] = $moduleName;
            }
        }

        return $modulesList;
    }
    
    function listContexts()
    {
        $contextsList = [];
        
        foreach( $this->siteHeritages as $siteItem )
        {
            if( $siteItem == "global" ){
                $dir = "sites/default/".Context::CONTROLLER_SUBFOLDER;
            }
            else {
                $dir = "sites/".$siteItem."/".Context::CONTROLLER_SUBFOLDER;
            }
            
            if( is_dir($dir) ){
                foreach( array_diff(scandir( $dir ), ['..', '.'] ) as $contextFile )
                {
                    $contextName = substr($contextFile, 0, strripos($contextFile, ".php"));
                    
                    if( !in_array($contextName, $contextsList) ){
                        $contextsList[] = $contextName;
                    }
                }
            }
        }
        
        sort($contextsList);
        
        return $contextsList;
    }
    
    function setRootUrl( string $rootUrl )
    {
        $this->rootUrl = $rootUrl;
        
        return $this;
    }
    
    function getRootUrl()
    {
        if( !$this->rootUrl ){
            $this->rootUrl = ($this->baseUri)? $this->baseUri: '/';
        }
        return $this->rootUrl;
    }    
}
