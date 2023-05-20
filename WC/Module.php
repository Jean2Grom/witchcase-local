<?php
namespace WC;

class Module 
{
    const DESIGN_SUBFOLDER              = "design/modules";
    const DESIGN_INCLUDES_SUBFOLDER     = "design/modules/includes";

    const DIR = "modules";
    
    var $name;
    var $execFile;
    var $designFile;
    var $result;
    var $config;
    var $maxStatus;
    
    /** @var Witch */
    var $witch;
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( Witch $witch, string $moduleName )
    {
        $this->witch    = $witch;
        $this->wc       = $this->witch->wc;
                
        if( strcasecmp(substr($moduleName, -4), ".php") == 0 ){
            $moduleName =  substr($moduleName, 0, -4);
        }        
        
        $this->name     = $moduleName;
        $this->execFile = $this->wc->website->getFilePath( self::DIR.'/'.$this->name.".php" );
        
        $this->config = [];
        foreach( array_reverse($this->wc->website->siteHeritages) as $siteItem )
        {
            $configModulesItem = $this->wc->configuration->read($siteItem, "modules");
            $this->config = array_replace_recursive(
                $this->config, 
                $configModulesItem[ $this->name ] ?? []
            );
        }
        
        $this->maxStatus = 0;
        foreach( $this->wc->user->policies as $policy ){
            if( $policy["module"] == $this->name || $policy["module"] == '*' ){
                if( $policy["status"] == '*' ){
                    $this->maxStatus = 999999999;
                }
                elseif( $policy["status"] > $this->maxStatus ){
                    $this->maxStatus = (int) $policy["status"];
                }
            }
        }
    }
    
    function execute()
    {
        if( !$this->execFile ){
            $this->wc->log->error("Can't access module file: ".$this->name, true);
        }

        ob_start();
        include $this->execFile;   
        $result = ob_get_contents();
        ob_end_clean();
        
        $this->result = $result;
        
        return $this->result;
    }
    
    
    function setResult( $result )
    {
        $this->result = $result;
        return $this;
    }

    function getResult(){
        return $this->result;
    }
    
    function getDesignFile( $designName=false, $mandatory=true )
    {
        if( !empty($this->designFile) ){
            return $this->designFile;
        }
        
        if( !$designName ){
            $designName = $this->name.".php";
        }
        elseif( strcasecmp(substr($designName, -4), ".php") != 0 ){
            $designName .=  ".php";
        }
        
        $filename           = self::DESIGN_SUBFOLDER."/".$designName;
        $this->designFile   = $this->wc->website->getFilePath( $filename );
        
        if( !$this->designFile ){
            $this->wc->log->error("Can't get design file: ".$filename, $mandatory);
        }
        
        return $this->designFile;
    }
    
    function getImageFile( $filename ){
        return $this->wc->website->context->getImageFile( $filename );
    }
    
    function addCssFile( $cssFile ){
        return $this->wc->website->context->addCssFile( $cssFile );
    }
    
    function getCssFiles(){
        return $this->wc->website->context->getCssFiles();
    }

    function addJsFile( $jsFile ){
        return $this->wc->website->context->addJsFile( $jsFile );
    }
    
    function addJsLibFile( $jsFile ){
        return $this->wc->website->context->addJsLibFile( $jsFile );
    }
    
    function getJsFiles(){
        return $this->wc->website->context->getJsFiles();
    }
    
    function setContext( $context ){
        return $this->wc->website->context->setExecFile( $context );
    }
    
    function getIncludeDesignFile( $filename )
    {
        $fullPath = $this->wc->website->getFilePath(self::DESIGN_INCLUDES_SUBFOLDER."/".$filename );
        
        if( !$fullPath ){
            return false;
        }
        
        return $fullPath;
    }
    
    function getDaughters( Witch $witch=NULL )
    {
        if( empty($witch) ){
            $witch = $this->witch;
        }
        
        $daughters = [];
        foreach( $witch->daughters as $daughterKey => $daughterWitch ){
            if( $daughterWitch->statusLevel <= $this->maxStatus ){
                $daughters[ $daughterKey ] = $daughterWitch;
            }
        }
        
        return $daughters;
    }
}