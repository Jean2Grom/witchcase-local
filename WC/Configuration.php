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
        if( $variable && !empty($this->configuration[ $section ][ $variable ]) ){
            return $this->configuration[ $section ][ $variable ];
        }
        elseif( $variable && !empty($this->sites[ $section ][ $variable ]) ){
            return $this->sites[ $section ][ $variable ];
        }
        elseif( !$variable && !empty($this->configuration[ $section ]) ){
            return $this->configuration[ $section ];
        }
        elseif( !$variable && !empty($this->sites[ $section ]) ){
            return $this->sites[ $section ];
        }
        
        return null;
    }
    
    function readSiteVar( string $variable, Website $website=null )
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
    
    function getHeritedVariable( $variable, $site )
    {
        if( isset($this->heritedVariables[ $variable ][ $site ]) ){
            return $this->heritedVariables[ $variable ][ $site ];
        }
        
        if( !isset($this->heritedVariables[ $variable ]) ){
            $this->heritedVariables[ $variable ] = [];
        }
        
        $this->heritedVariables[ $variable ][ $site ] = [];
        
        $sections = $this->configurationAreas( $site );
        
        foreach( $sections as $section )
        {
            $sectionVariables = $this->read($section, $variable);
            
            if( is_array($sectionVariables) ){
                foreach( $sectionVariables as $sectionVariableName => $sectionVariableValue ){
                    $this->heritedVariables[$variable][$site][$sectionVariableName] = $sectionVariableValue;
                }
            }
        }
        
        return $this->heritedVariables[ $variable ][ $site ];
    }
    
    function addVariable( $varSection, $varName, $varValue, $isArray=false )
    {
        $filename           = basename( $this->filepath );
        $dirname            = substr( $this->filepath, 0, -strlen($filename) );
        $filenameExtension  = pathinfo( $filename, PATHINFO_EXTENSION );
        $baseFilename       = substr( $filename, 0, (-1 -strlen( $filenameExtension )) );
        $workFilename       = $baseFilename."_tmp.".$filenameExtension;
        $workFilepath       = $dirname.$workFilename;
        
        if( file_exists($workFilepath) ){
            unlink($workFilepath);
        }
        
        if( is_array($varValue) ){
            $isArray = true;
        }
        elseif( $isArray ){
            $varValue = [ $varValue ];
        }
        
        if( isset($this->$varSection) )
        {
            $sectionContent = $this->$varSection;
            
            if( isset($sectionContent[ $varName ]) )
            {
                if( is_array($sectionContent[$varName]) && $isArray ){
                    foreach( $varValue as $varValue_item ){
                        if( !in_array($varValue_item, $sectionContent[$varName]) ){
                            $sectionContent[$varName][] = $varValue_item;   
                        }
                    }
                }
                elseif( is_array($sectionContent[$varName]) ){
                    if( !in_array($varValue, $sectionContent[$varName]) ){
                        $sectionContent[$varName][] = $varValue;
                    }   
                }
                else {
                    $sectionContent[$varName] = $varValue;
                }
            }
            else {
                $sectionContent[$varName]   = $varValue;
            }
            
            $this->$varSection = $sectionContent;
        }
        else
        {
            $this->sections[]   = $varSection;
            $this->$varSection  = [ $varName => $varValue ];
        }
        
        $fileContent = "; this is the INI configuration file\n";
        
        foreach( $this->sections as $section )
        {
            $fileContent .= "\n[".$section."]\n";
            
            $sectionContent =   $this->$section;
            foreach( $sectionContent as $sectionVariableName => $sectionVariableValue )
            {   
                if( !is_array($sectionVariableValue) ){
                    $fileContent .= $sectionVariableName."=".$sectionVariableValue."\n";
                }
                else {
                    foreach( $sectionVariableValue as $sectionVariableValue_item ){
                        $fileContent .= $sectionVariableName."[]=".$sectionVariableValue_item."\n"; 
                    }
                }
            }
        }
        
        $fp = fopen($workFilepath, "w");
        fwrite($fp, $fileContent);
        fclose($fp);
        
        $saveFilename = $baseFilename."_".date("Y-m-d_H-i-s").".".$filenameExtension;
        
        if(     !copy($this->filepath, $dirname.$saveFilename) 
            ||  !rename( $workFilepath , $this->filepath )
        ){
            return false;
        }
        
        return $saveFilename;
    }
    
    function getSitesAccess()
    {
        if( !empty($this->siteAccess) ){
            return  $this->siteAccess;
        }
        
        foreach( $this->sites as $site => $siteData )
        {
            if( empty($siteData['access']) ){
                continue;
            }
            
            $this->siteAccess[ $site ] = $siteData['access'];
            
            foreach( $siteData['access'] as $siteAccess ){
                $this->siteAccess[ $site ][] = $siteAccess;
            }
        }
        
        return $this->siteAccess;
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
    
    function getAdministratedSites( $site )
    {
        if( isset($this->administratedSites[$site]) ){
            return $this->administratedSites[$site];
        }
        
        $this->administratedSites   = [];
        $section                    = $this->$site;
        
        foreach( $section['adminForSites'] as $administratedSite )
        {
            if(strcmp($administratedSite, '*') == 0 )
            {
                $this->administratedSites = $this->global['sites'];
                break;
            }
            
            if( !in_array($administratedSite, $this->global['sites']) )
            {
                $message =  "Site ".$administratedSite;
                $message .= " declared to be administrated by site ".$site;
                $message .= " is no active site declared in the [global] part of configuration";
                $this->wc->log->error($message);
                continue;
            }
            
            $this->administratedSites[] = $administratedSite;
        }
        
        return $this->administratedSites;
    }
    
    function configurationAreas( $site )
    {
        if( isset($this->configurationAreas[ $site ]) ){
            return $this->configurationAreas[ $site ];
        }
        
        if( !isset($this->configurationAreas) ){
            $this->configurationAreas = [];
        }
        
        $this->configurationAreas[ $site ] = [ $site ];
        
        $siteHeritages  = $this->read($site, 'siteHeritages');
        if( is_array($siteHeritages) ){
            foreach( $siteHeritages as $heritedSite ){
                $this->configurationAreas[ $site ][] = $heritedSite;
            }
        }
        
        $this->configurationAreas[ $site ][] = 'global';
        
        return $this->configurationAreas[ $site ];
    }
    
    function getExtensions( $site )
    {
        return $this->getHeritedVariable( 'extensions', $site );
    }
    
    function getAllAttributes( $site )
    {
        return $this->getHeritedVariable( 'attributes', $site );
    }
    
    function getModules( $site )
    {
        return $this->getHeritedVariable( 'modules', $site );
    }
    
    function getModulesActions( $site )
    {
        if( isset($this->modulesActions[ $site ]) ){
            return $this->modulesActions[ $site ];
        }
        
        if( !isset($this->modulesActions) ){
            $this->modulesActions = [];
        }
        
        $this->modulesActions[ $site ] = [];
        
        $modules    = $this->getModules( $site );
        $sections   = $this->configurationAreas( $site );
        
        foreach( $modules as $module )
        {
            $this->modulesActions[ $site ][ $module ] = [ '*' ];
            
            foreach( $sections as $section )
            {
                //$moduleActions = $this->get( $section, $module.'.actions' );
                //if( is_array($moduleActions) ){
                //    foreach( $moduleActions as $action ){
                
                $modulesData = $this->read( $section, 'modules' );
                
                if( is_array($modulesData["actions"]) ){
                    foreach( $modulesData["actions"] as $action ){
                        $this->modulesActions[ $site ][ $module ][] = $action;
                    }
                }
            }
        }
        
        return $this->modulesActions[ $site ];
    }
    
    function getModulesVariables( $site )
    {
        if( isset($this->modules[ $site ]) ){
            return $this->modules[ $site ];
        }
        
        if( !isset($this->modules) ){
            $this->modules = [];
        }
        
        $this->modules[ $site ] = [];
        
        $modules    = $this->getModules( $site );
        $sections   = $this->configurationAreas( $site );
        
        foreach( $modules as $module )
        {
            $this->modules[ $site ][ $module ] = [];
            
            foreach( $sections as $section ){
                foreach( $this->$section as $varIniName => $varValue ){
                    if( strpos( $varIniName , $module."."  ) === 0 )
                    {
                        $varName = substr( $varIniName , strlen($module.".") );
                        $this->modules[ $site ][ $module ][ $varName ] = $varValue;
                    }
                }   
            }
        }
        
        return $this->modules[ $site ];
    }
    
    function rollback( $iniSave )
    {
        $filename           = basename($this->filepath);
        $dirname            = substr( $this->filepath, 0, -strlen($filename) );
        
        if( !file_exists($dirname.$iniSave) ){
            return false;
        }
        
        return copy( $dirname.$iniSave, $this->filepath );
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
