<?php
namespace WC;

/**
 * Class containing website (app) information and aggreging related objects
 * 
 * @author Jean2Grom
 */
class Website 
{
    const SITES_DIR         = "sites";
    const DEFAULT_SITE_DIR  = "sites/default";
    
    public $name;
    public $currentAccess;
    public $site;
    
    public $access;
    public $adminForSites;
    public $sitesRestrictions;
    
    public $baseUri;
    public $urlPath;
    public $modulesList;
    private $rootUrl;
    
    public $modules;
    public $attributes;
    public $status;
    
    public $debug;    
    public $extensions;
    
    public $siteHeritages;
    
    public $defaultContext;
    
    /**
     * Layout class that handle display
     * @var Context
     */    
    public Context $context;
    
    /**
     * class that handles witch summoning and modules invocation
     * @var Cairn
     */
    public Cairn $cairn;
    
    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;
    
    function __construct( WitchCase $wc, string $name, ?string $siteAccess=null )
    {
        $this->wc               = $wc;
        $this->name             = $name;
        
        // Reading non heritable confs publiciables
        $this->access               = $this->wc->configuration->read($this->name, "access");
        $this->adminForSites        = $this->wc->configuration->read($this->name, "adminForSites");
        $this->site                 = $this->wc->configuration->read($this->name, "visibility") ?? $this->name;
        
        if( $this->site !== $this->name ){
            $this->wc->debug->toResume("URL site is acceded by: \"".$this->site."\"", 'WEBSITE');
        }
        
        
        $this->siteHeritages        = $this->wc->configuration->getSiteHeritage( $this->name );
        $this->siteHeritages[]      = "global";
        
        $this->modules              = $this->wc->configuration->readSiteMergedVar('modules', $this) ?? [];
        $witchesConf                = $this->wc->configuration->readSiteMergedVar('witches', $this) ?? [];
        
        $this->debug                = $this->wc->configuration->readSiteVar('debug', $this);
        $this->status               = $this->wc->configuration->readSiteVar('status', $this);
        $defaultContext             = $this->wc->configuration->readSiteVar('defaultContext', $this);
        if( !empty($defaultContext) ){
            $this->defaultContext       = $defaultContext;
        }
        
        $this->sitesRestrictions    = [ $this->site ];
        foreach( $this->adminForSites ?? [] as $adminisratedSite )
        {
            if( $adminisratedSite == '*' )
            {
                $this->sitesRestrictions = false;
                break;
            }
            
            $this->sitesRestrictions[] = $adminisratedSite;
        }
        
        $this->currentAccess    = $siteAccess ?? array_values($this->access ?? [])[0] ?? '';
        $firstSlashPosition     = strpos($this->currentAccess, '/');
        $this->baseUri          = ($firstSlashPosition !== false)? substr( $this->currentAccess, $firstSlashPosition ): '';
        $this->urlPath          = Tools::urlCleanupString( substr( $this->wc->request->access, strlen($this->currentAccess) ) );
        
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
        $this->context  = new Context( $this, $this->defaultContext );
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
            'site'  => $this->site,
            'url'   => $this->urlPath,
        ];
    }
    
    function display()
    {
        $this->context->display();
        
        return $this;
    }
    
    
    function getFilePath( string $filename ): ?string
    {
        // Looking in this site
        $filePath = self::SITES_DIR.'/'.$this->name.'/'.$filename;
        
        if( is_file($filePath) ){
            return $filePath;
        }
        
        // Looking in herited sites
        foreach( $this->siteHeritages as $heritedSite )
        {
            $filePath = self::SITES_DIR.'/'.$heritedSite.'/'.$filename;

            if( file_exists($filePath) ){
                return $filePath;
            }
        }
        
        // Looking in extension files
        /*
        $extensions = $this->wc->configuration->readSiteMergedVar('extensions', $this);
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
        $filePath = self::DEFAULT_SITE_DIR.'/'.$filename;
        
        if( is_file($filePath) ){
            return $filePath;
        }
        
        // Looking in system files
        $filePath = "system/".$filename;
        
        if( is_file($filePath) ){
            return $filePath;
        }
        
        return null;
    }
    
    
    function getWebPath( string $filename ): ?string
    {
        $filePath = $this->wc->website->getFilePath( $filename );
        
        if( $filePath ){
            return '/'.$filePath;
        }
        
        return null;
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
                $dir = self::DEFAULT_SITE_DIR.'/'.Module::DIR;
            }
            else {
                $dir = self::SITES_DIR.'/'.$siteItem."/".Module::DIR;
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
                $dir = "sites/default/".Context::DIR;
            }
            else {
                $dir = "sites/".$siteItem."/".Context::DIR;
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
    
    function getFullUrl( string $urlPath='', ?array $urlParams=null, ?Request $request=null )
    {
        if( !$request ){
            $request = $this->wc->request;
        }
        
        $fullUrl    =   $request->protocole.'://';
        if( strstr($this->currentAccess, '/') ){
            $fullUrl .= dirname($this->currentAccess);
        }
        else {
            $fullUrl .= $this->currentAccess;
        }
        
        if( substr($fullUrl, -( strlen($request->port) + 1 )) !== ':'.$request->port ){
            $fullUrl .= ':'.$request->port;
        }
        
        $fullUrl    .=  $this->getUrl( $urlPath, $urlParams );
        
        return $fullUrl;
    }
    
    function getUrl( string $urlPath='', ?array $urlParams=null )
    {
        $url    = $this->baseUri;
        if( !empty($urlPath) && !str_starts_with($urlPath, '/') ){
            $url .= '/';
        }
        $url    .=  $urlPath;
        
        if( empty($url) ){
            $url = '/';
        }
        
        if( !empty($urlParams) ){
            $url .= '?'.http_build_query($urlParams);
        }

        return $url;
    }
}
