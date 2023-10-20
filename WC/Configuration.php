<?php 
namespace WC;

class Configuration 
{
    const WC_ENV_VAR_PREFIX = 'WC_';    
    const DEFAULT_DIRECTORY = "configuration";
    const CONFIG_FILE       = 'configuration.json';
    const SITES_FILE        = 'sites.json';
    
    var $filepath           = "configuration/configuration.ini";
    var $sections           = [];
    var $siteAccess         = [];
    var $heritedVariables   = [];
    
    
    var string $dir;
    var $configuration  = [];
    var $sites          = [];
    
    /** 
     * container
     * @var WitchCase 
     */
    var $wc;
    
    /**
     * @param WitchCase $wc : container
     * @param string $configurationDirectory : path to configuration files directory
     * @param boolean $mandatory : if set to true, die process if configuration files not found
     */
    function __construct( WitchCase $wc, string $configurationDirectory=null, bool $mandatory=true )
    {
        $this->wc = $wc;
        
        if( $configurationDirectory ){   
            $this->dir = $configurationDirectory;
        }
        else {
            $this->dir = self::DEFAULT_DIRECTORY;
        }
        
        if( $mandatory
            &&  ( !file_exists($this->dir.'/'.self::CONFIG_FILE) 
                || !file_exists($this->dir.'/'.self::SITES_FILE)  )
        ){
            die("Configuration files unreachable");
        }
        
        $rawConfiguration   = file_get_contents( $this->dir.'/'.self::CONFIG_FILE );
        $rawSites           = file_get_contents( $this->dir.'/'.self::SITES_FILE );
        
        $wcEnvVars = [];
        foreach( getenv() as $envVarName => $envVarValue ){
            if( str_starts_with($envVarName, self::WC_ENV_VAR_PREFIX) ){
                $wcEnvVars['<'.$envVarName.'>'] = $envVarValue;
            }
        }
        
        $rawConfigurationEnvVarIntegrated   = str_replace( array_keys($wcEnvVars), array_values($wcEnvVars), $rawConfiguration );
        $rawSitesEnvVarIntegrated           = str_replace( array_keys($wcEnvVars), array_values($wcEnvVars), $rawSites );
        
        $this->configuration    = json_decode($rawConfigurationEnvVarIntegrated, true);
        $this->sites            = json_decode($rawSitesEnvVarIntegrated, true);

        if( $mandatory
            &&  ( empty($this->configuration) 
                || empty($this->sites)  )
        ){
            die("Configuration files misformatted");
        }
    }
    
    function read( string $section, string $variable=null)
    {
        if( $variable && isset($this->configuration[ $section ][ $variable ]) ){
            return $this->configuration[ $section ][ $variable ];
        }
        elseif( $variable && isset($this->sites[ $section ][ $variable ]) ){
            return $this->sites[ $section ][ $variable ];
        }
        elseif( !$variable && isset($this->configuration[ $section ]) ){
            return $this->configuration[ $section ];
        }
        elseif( !$variable && isset($this->sites[ $section ]) ){
            return $this->sites[ $section ];
        }
        
        return null;
    }
    
    function readSiteVar( string $variable, ?Website $website=null )
    {
        if( !$website ){
            $website = $this->wc->website;
        }
        
        foreach( $website->siteHeritages as $section )
        {
            $value = $this->read($section, $variable);
            
            if( !is_null($value) ){
                return $value;
            }
        }
        
        return null;
    }
    
    function readSiteMergedVar( string $variable, ?Website $website=null )
    {
        if( !$website ){
            $website = $this->wc->website;
        }
        
        $return = null;
        foreach( array_reverse($website->siteHeritages) as $section )
        {
            $value = $this->read($section, $variable);
            
            if( is_array($value) ){
                $return = array_replace_recursive($return ?? [], $value);
            }
            elseif( !is_null($value) ){
                $return = $value;
            }
        }
        
        return $return;
    }
    
    
    function getSiteAccessMap()
    {
        $map = [];
        foreach( $this->sites as $site => $siteData )
        {
            if( empty($siteData['access']) ){
                continue;
            }
            
            foreach( $siteData['access'] as $siteaccess ){
                $map[ $siteaccess ] = $site;
            }
        }
        
        return $map;
    }
    
    
    /**
     * Reccursive function for reading heritages configuration cascades
     * 
     * @param string $siteName : configuration name of site to ckeck
     * @return array : ordered list of sites that are herited from
     */
    function getSiteHeritage( string $siteName ): array
    {
        $siteHeritages      = $this->read( $siteName, "siteHeritages" );
        
        if( !$siteHeritages ){
            return [ $siteName ];
        }
        
        $return = [ $siteName ];
        foreach( $siteHeritages as $siteHeritagesItem )
        {
            $return[] = $siteHeritagesItem;                
            foreach( $this->getSiteHeritage($siteHeritagesItem) as  $subSiteHeritagesItem ){
                $return[] = $subSiteHeritagesItem;                
            }
        }
        
        return array_unique($return);
    }    
}
