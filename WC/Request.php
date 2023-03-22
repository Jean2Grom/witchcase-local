<?php
namespace WC;

use WC\Website;

class Request
{
    var $method;
    var $protocoleName;
    var $protocole;
    var $https;
    var $host;
    var $port;
    var $uri;
    var $url;
    var $queryString;
    var $requesterIpAddress;
    
    /** @var Website */
    var $website;
    
    /** @var WitchCase */
    var $wc;
    
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
        $this->url                  = filter_input(INPUT_SERVER, "SCRIPT_URL")
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
           $this->uri =  $this->protocole."://".$this->host.$this->url;
        }
    }
    
    function param( string $name )
    {
        if( $this->method == 'POST' ){
            $paramType = INPUT_POST;
        }
        else {
            $paramType = INPUT_GET;
        }
        
        return filter_input($paramType, $name) ?? false;
    }
    
    function getWebsite()
    {
        if( !empty($this->website) ){
            return $this->website;
        }
        
        $parsed_url     = parse_url( strtolower($this->uri ?? '/') );

        // Determinating which site is acceded comparing
        // Configuration and URI
        $access         = $parsed_url["host"].$parsed_url['path'];

        $compareAccess  = $this->compareAccess($access);
        
        // if no match and access has "www" for subomain, try whithout (considered default subdomain)
        if( !$compareAccess['matchedSiteAccess'] && strcasecmp(substr($access, 0, 4), "www.") == 0 )
        {
            $access         = substr($access, 4);
            $compareAccess  = $this->compareAccess( $access );
        }
        
        if( !$compareAccess['matchedSiteAccess']   ){   
            $this->wc->log->error("Site access is not in configuration file", true);  
        }
        else
        {
            $message = "Accessing site: ".$compareAccess['siteName']  .", with site access: ".$compareAccess['matchedSiteAccess'];
            $this->wc->debug->dump($message);
        }
        
        $this->website = new Website( $this->wc, $compareAccess['siteName']  , $compareAccess['matchedSiteAccess'] );
        
        return $this->website->urlSetup($access);
    }
    
    private function compareAccess( $access )
    {
        $siteName           = false;
        $matchedSiteAccess  = false;
        $matchDegree        = 0;
        foreach( $this->wc->configuration->getSiteAccessMap() as $siteAccess => $site )
        {
            if( strcasecmp($access, $siteAccess) === 0
                || (
                    stripos($access, $siteAccess) === 0 
                    && strlen($siteAccess) > $matchDegree 
                    && strlen($access) > strlen($siteAccess) 
                    && substr($access, strlen($siteAccess), 1) == '/'
                )
            ){
                $matchDegree        = strlen($siteAccess);
                $siteName           = $site;
                $matchedSiteAccess  = $siteAccess;
                
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
