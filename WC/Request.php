<?php
namespace WC;

use WC\Website;

/** 
 * Class handeling HTTP Request, 
 * determining URL targeted website and ressource
 * 
 * @author Jean2Grom
 */
class Request
{
    const DEFAULT_SITE      = "blank";
    
    public $method;
    public $protocoleName;
    public $protocole;
    public $https;
    public $host;
    public $port;
    public $uri;
    public $path;
    public $queryString;
    public $requesterIpAddress;
    public $access;
    
    /** 
     * Class containing website (app) information and aggreging related objects
     * @var Website 
     */
    public Website $website;
    
    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;
    
    function __construct( WitchCase $wc )
    {
        $this->wc = $wc;
        
        $this->method               = filter_input(INPUT_SERVER, "REQUEST_METHOD")
                                        ?? $_SERVER["REQUEST_METHOD"];
        $this->protocoleName        = filter_input(INPUT_SERVER, "SERVER_PROTOCOL")
                                        ?? $_SERVER["SERVER_PROTOCOL"];
        $this->https                = filter_input(INPUT_SERVER, "HTTPS", FILTER_DEFAULT, FILTER_NULL_ON_FAILURE)
                                        ?? $_SERVER["HTTPS"];
        $this->protocole            = filter_input(INPUT_SERVER, "HTTP_X_FORWARDED_PROTO", FILTER_DEFAULT, FILTER_NULL_ON_FAILURE)
                                        ?? $_SERVER["HTTP_X_FORWARDED_PROTO"];
        $this->host                 = filter_input(INPUT_SERVER, "HTTP_HOST")
                                        ?? $_SERVER["HTTP_HOST"];
        $this->port                 = filter_input(INPUT_SERVER, "SERVER_PORT")
                                        ?? $_SERVER["SERVER_PORT"];
        $this->uri                  = filter_input(INPUT_SERVER, "SCRIPT_URI", FILTER_DEFAULT, FILTER_NULL_ON_FAILURE)
                                        ?? $_SERVER["SCRIPT_URI"];
        $this->path                 = filter_input(INPUT_SERVER, "REQUEST_URI", FILTER_DEFAULT, FILTER_NULL_ON_FAILURE)
                                        ?? $_SERVER["REQUEST_URI"]
                                        ?? filter_input(INPUT_SERVER, "SCRIPT_URL")
                                        ?? $_SERVER["PATH_INFO"] ?? "/";
        $this->queryString          = filter_input(INPUT_SERVER, "QUERY_STRING")
                                        ?? $_SERVER["QUERY_STRING"] ?? "";
        $this->requesterIpAddress   = self::getRequesterIpAddress();
        
        if( empty($this->protocole) && !empty($this->https) && $this->https == "on" ){
            $this->protocole = "https";
        }
        elseif( empty($this->protocole) ){
            $this->protocole = "http";
        }
        
        if( !$this->uri ){
           $this->uri =  $this->protocole."://".$this->host.$this->path;
        }
    }
    
    function param( string $name, mixed $method=false, int $filter=FILTER_DEFAULT, mixed $secondaryFiler=0 )
    {
        if( (!$method && $this->method == 'POST') || (strtolower( $method ) == 'post') ){
            $paramType = INPUT_POST;
        }
        else {
            $paramType = INPUT_GET;
        }
        
        return filter_input($paramType, $name, $filter, $secondaryFiler);
    }
    
    function getWebsite()
    {
        if( empty($this->website) )
        {
            // Determinating which site is acceded comparing
            // Configuration and URI
            $parsed_url     = parse_url( strtolower($this->uri ?? '/') );
            $this->access   = $parsed_url["host"].$parsed_url['path'];
            $compareAccess  = $this->compareAccess($this->access );

            // if no match and access has "www" for subomain, try whithout (considered default subdomain)
            if( !$compareAccess['matchedSiteAccess'] && str_starts_with($this->access , "www.") )
            {
                $this->access   = substr($this->access , 4);
                $compareAccess  = $this->compareAccess( $this->access  );
            }
            
            if( !$compareAccess['matchedSiteAccess']   ){   
                $this->wc->log->error("Site access is not in configuration file");
            }
            else {
                $this->wc->debug->toResume("Accessing site: \"".$compareAccess['siteName']."\", with site access: \"".$compareAccess['matchedSiteAccess']."\"", 'SITEACCESS');
            }
            
            $this->website = new Website( $this->wc, $compareAccess['siteName']  , $compareAccess['matchedSiteAccess'] );
        }
        
        return $this->website;
    }
    
    function getFullUrl( string $urlPath='', ?Website $website=null )
    {
        if( !$website ){
            $website = $this->website;
        }
        
        return $this->website->getFullUrl($urlPath, $this);
    }
    
    private function compareAccess( $access )
    {
        $haystack           = strtolower( $access );
        $siteName           = $this->wc->configuration->read('system','defaultSite') ?? self::DEFAULT_SITE;
        
        $matchedSiteAccess  = "";
        $matchDegree        = 0;
        foreach( $this->wc->configuration->getSiteAccessMap() as $siteAccess => $site )
        {
            $needle = strtolower( $siteAccess );
            
            if( $haystack === $needle
                || (str_starts_with( $haystack, $needle.'/' ) && strlen( $siteAccess ) > $matchDegree)
            ){
                $matchDegree        = strlen($siteAccess);
                $siteName           = $site;
                $matchedSiteAccess  = $siteAccess;
            }
        }
        
        return [ 'siteName' => $siteName, 'matchedSiteAccess' => $matchedSiteAccess ];
    }
    
    static function getRequesterIpAddress()
    {
        return filter_input( 
            INPUT_SERVER, 
            'REMOTE_ADDR', 
            FILTER_VALIDATE_IP 
        ) ?? 
        ( substr( filter_input(INPUT_SERVER, 'HTTP_HOST'), 0, 9) === 'localhost' )? '127.0.0.1': 
            filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_VALIDATE_IP);
    }
}
