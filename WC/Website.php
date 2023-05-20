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
    var $isSkinForSite;    
    var $sitesRestrictions;
    
    var $baseUri;
    var $urlPath;
    var $modulesList;
    private $rootUrl;
    
    
    var $modules;
    var $attributes;
    var $status;
    
    var $extensions;
    
    var $siteHeritages;
    var $context;
    
    /** @var WitchCase */
    var $wc;
    
    /** @var Cairn */
    var $cairn;
    
    function __construct( WitchCase $wc, string $name, ?string $siteAccess=null )
    {
        $this->wc               = $wc;
        $this->name             = $name;
        
        $this->access               = $this->wc->configuration->read($this->name, "access");
        $this->adminForSites        = $this->wc->configuration->read($this->name, "adminForSites");
        $this->isSkinForSite        = $this->wc->configuration->read($this->name, "isSkinForSite");
        
        $this->siteHeritages        = $this->wc->configuration->getSiteHeritage( $this->name );
        $this->siteHeritages[]      = "global";
        
        $this->modules              = $this->wc->configuration->readSiteVar('modules', $this) ?? [];
        $witchesConf                = $this->wc->configuration->readSiteVar('witches', $this) ?? [];
        
        $this->sitesRestrictions    = [ $this->name ];
        foreach( $this->adminForSites ?? [] as $adminisratedSite )
        {
            if( $adminisratedSite == '*' )
            {
                $this->sitesRestrictions = false;
                break;
            }
            
            $this->sitesRestrictions[] = $adminisratedSite;
        }        
        
        $this->currentAccess    = $siteAccess ?? array_values($this->access)[0];
        $firstSlashPosition     = strpos($this->currentAccess, '/');
        $this->baseUri          = ($firstSlashPosition !== false)? substr( $this->currentAccess, $firstSlashPosition ): '';
        $this->urlPath          = Witch::urlCleanupString( substr( $this->wc->request->access, strlen($this->currentAccess) ) );
        
        foreach( $this->modules as $moduleName => $moduleConf ){
            foreach( $moduleConf['witches'] ?? [] as $moduleWitchName => $moduleWitchConf ){
                if( empty($witchesConf[ $moduleWitchName ]) )
                {
                    $witchesConf[ $moduleWitchName ] = array_replace_recursive( 
                        $moduleWitchConf, 
                        [ 'module' => $moduleName ] 
                    );
                }
            }
        }
        
        $this->cairn    = new Cairn( $this->wc, $witchesConf, $this );
        $this->context  = new Context( $this );
    }
    
    function get(string $name): mixed {
        return $this->wc->configuration->readSiteVar($name, $this);
    }
    
    function getCairn(): Cairn {
        return $this->cairn;
    }
    
    function getUrlSearchParameters()
    {
        return [
            'site'  => $this->isSkinForSite ?? $this->name,
            'url'   => $this->urlPath,
        ];
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
    
    function getFullUrl( string $urlPath='', ?Request $request=null )
    {
        if( !$request ){
            $request = $this->wc->request;
        }
        
        $fullUrl    =   $request->protocole.'://';
        $fullUrl    .=  $request->host;
        
        $fullUrl    .=  $this->getUrl( $urlPath );
        
        return $fullUrl;
    }
    
    function getUrl( string $urlPath='' )
    {
        $url    = $this->baseUri;
        if( !empty($urlPath) && !str_starts_with($urlPath, '/') ){
            $url .= '/';
        }
        $url    .=  $urlPath;
        
        return $url;
    }
}
