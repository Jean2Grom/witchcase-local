<?php

namespace WC;

class Context 
{
    const CONTROLLER_SUBFOLDER      = "controller/contexts";
    const DESIGN_SUBFOLDER          = "design/contexts";
    const DESIGN_INCLUDES_SUBFOLDER = "design/contexts/includes";
    const IMAGES_SUBFOLDER          = "design/images";
    const JS_SUBFOLDER              = "design/js";
    const CSS_SUBFOLDER             = "design/css";
    const FONTS_SUBFOLDER           = "design/fonts";
    
    var $name;
    var $execFile;
    var $designFile;
    var $website;
    
    private $css    = [];
    private $js     = [];
    private $jsLib  = [];
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( Website $website, $contextFile=false )
    {
        $this->website  = $website;
        $this->wc       = $this->website->wc;
        
        if( !empty($contextFile) )
        {
            if( strcasecmp(substr($contextFile, -4), ".php") != 0 ){
                $contextFile .=  ".php";
            }
            
            $this->execFile = $this->website->getFilePath( self::CONTROLLER_SUBFOLDER."/".$contextFile );
            
            if( !$this->execFile ){
                $this->wc->log->error("Context File: ".$contextFile." summoned in module but not found");
            }
        }
    }
    
    function setExecFile( $contextFile, $force=false )
    {
        if( !empty($this->execFile) && !$force ){
            return $this;
        }
        
        if( strcasecmp(substr($contextFile, -4), ".php") != 0 ){
            $contextFile .= ".php";
        }
        
        $this->execFile = $this->wc->website->getFilePath( self::CONTROLLER_SUBFOLDER."/".$contextFile );
        
        return $this;
    }
    
    function getDesignFile( $designFile=false, $mandatory=true )
    {
        if( $this->designFile ){
            return $this->designFile;
        }
        
        if( !$this->execFile ){
            $this->getExecFile();
        }
        
        if( !$designFile ){
            $designFile = basename( $this->getExecFile() );
        }
        elseif( strcasecmp(substr($designFile, -4), ".php") != 0 ){
            $designFile .=  ".php";
        }
        
        $this->designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/".$designFile );
        
        if( !$this->designFile ){
            $this->wc->log->error("Can't get design file: ".$designFile, $mandatory);
        }
        
        return $this->designFile;
    }
    
    
    function addCssFile( $cssFile )
    {
        $cssFilePath = $this->wc->website->getFilePath( self::CSS_SUBFOLDER."/".$cssFile );
        
        if( $cssFilePath 
            && !in_array("/".$cssFilePath, $this->css)
        ){
            $this->css[] = "/".$cssFilePath;
        }
        else {
            return false;
        }
        
        return true;
    }
    
    function getCssFiles()
    {
        return $this->css;
    }

    function addJsFile( $jsFile )
    {
        $jsFilePath = $this->wc->website->getFilePath( self::JS_SUBFOLDER."/".$jsFile );
        
        if( $jsFilePath 
            && !in_array("/".$jsFilePath, $this->js) 
        ){
            $this->js[] = "/".$jsFilePath;
        }
        else {
            return false;
        }
        
        return true;
    }
    
    function getJsFiles()
    {
        return $this->js;
    }
    
    function addJsLibFile( $jsFile )
    {
        $jsFilePath = $this->wc->website->getFilePath( self::JS_SUBFOLDER."/".$jsFile );
        
        if( $jsFilePath 
            && !in_array("/".$jsFilePath, $this->jsLib) 
        ){
            $this->jsLib[] = "/".$jsFilePath;
        }
        else {
            return false;
        }
        
        return true;
    }
    
    function getJsLibFiles()
    {
        return $this->jsLib;
    }
    
    function getImageFile( $filename )
    {
        $fullPath = $this->wc->website->getFilePath(self::IMAGES_SUBFOLDER."/".$filename );
        
        if( !$fullPath ){
            return false;
        }
        
        return "/".$fullPath;
    }
    
    function getFontFile( $filename )
    {
        $fullPath = $this->wc->website->getFilePath(self::FONTS_SUBFOLDER."/".$filename );
        
        if( !$fullPath ){
            return false;
        }
        
        return "/".$fullPath;
    }
    
    function getIncludeDesignFile( $filename )
    {
        $fullPath = $this->wc->website->getFilePath(self::DESIGN_INCLUDES_SUBFOLDER."/".$filename );
        
        if( !$fullPath ){
            return false;
        }
        
        return $fullPath;
    }
    
    
    
    function getExecFile()
    {
        if( $this->execFile ){
            return $this->execFile;
        }
        
        // Context is in Location record
        // =============================
        if( $this->wc->localisation->context )
        {
            $this->execFile = $this->wc->website->getFilePath( self::CONTROLLER_SUBFOLDER."/".$this->wc->localisation->context );
            
            if( !$this->execFile )
            {
                $message    =   "Context File: ".$this->wc->localisation->context;
                $message    .=  " specified in localisation but not found";
                $this->wc->log->error($message);
            }
            else {
                return $this->execFile;
            }
        }
        
        // Context is in Target record
        // ===========================
        if( isset($target->context[ $this->wc->localisation->site ]) )
        {
            $this->execFile = $this->wc->module->getControllerFile( "contexts/".$target->context[ $this->wc->localisation->site ] );
            
            if( !$this->execFile )
            {
                $message    =   "Context File: ".$target->context[ $this->wc->localisation->site ];
                $message    .=  " specified in target but not found";
                $this->wc->log->error($message);
            }
            else {
                return $this->execFile;
            }
        }
        
        // Context is ruled in configuration file
        // ======================================
        if( !$this->execFile )
        {
            $this->execFile = $this->contextRuleConf( $this->wc->localisation->site );

            if( $this->execFile ){
                return $this->execFile;
            }
            
            $this->execFile = $this->contextRuleConf('global');
            
            if( $this->execFile ){
                return $this->execFile;
            }
        }
        
        // Context is default
        // ==================
        $this->execFile = $this->wc->module->getControllerFile( "contexts/default.php" );
        
        if( $this->execFile ){
            return $this->execFile;
        }
        
        $this->wc->log->error('No context file can be identified', true);
    }
    
    private function contextRuleConf( $confPart )
    {
        $contextRules   = $this->wc->configuration->read( $confPart, 'contextRules' );
        $contextValues  = $this->wc->configuration->read( $confPart, 'contextValues' );
        if( is_array($contextRules) && is_array($contextValues) ) 
        {
            if( count($contextRules) != count($contextValues)  )
            {
                $message    =   "Context rules and values don't match ";
                $message    .=  "(not same quantity) in the: ";
                $message    .=  $confPart." part of configuration file.";
                $this->wc->log->error( $message );
            }
            else
            {   
                foreach( $contextRules as $key => $rule_value )
                {
                    $buffer = explode('.', $rule_value);
                    $rule   = $buffer[0];
                    $value  = $buffer[1];
                    
                    $match   = false;
                    $exclude = false;
                    switch( $rule )
                    {
                        case 'target_structure':
                            if( $this->wc->localisation->has_target 
                                && ( strcmp($this->wc->localisation->target_structure, $value) == 0 )
                            ){
                                $match = true;
                            }
                            break;
                            
                        case 'parent_target_structure':
                            $parents = $this->wc->localisation->parents();
                            $parent_target_structure = "";
                            if( $parents[0]["target_table"] )
                            {
                                $buffer = explode('_', $parents[0]["target_table"]);
                                unset($buffer[0]);
                                $parent_target_structure = implode("_", $buffer);
                            }
                            
                            if( strcmp($parent_target_structure, $value) == 0 ){
                                $match = true;
                            }
                            break;
                            
                        case 'status':
                            if( strcmp($this->wc->localisation->status, $value) == 0 ){
                                $match = true;
                            }
                            break;
                            
                        case 'target_type':
                            if( $this->wc->localisation->has_target 
                                && ( strcmp($this->wc->localisation->target_type, $value) == 0 )
                            ){
                                $match = true;
                            }
                            break;
                            
                        case 'depth':
                            if( $this->wc->localisation->depth == $value ){
                                $match = true;
                            }
                            break;
                            
                        case 'subposition_parent':
                            $exclude = true;
                        case 'subposition_parent_included':
                            $positionMatch = explode(',', $value);
                            
                            $match = true;
                            if( $exclude && count($this->wc->localisation->position) <= count($positionMatch) ){
                                $match = false;
                            }
                            elseif( count($this->wc->localisation->position) < count($positionMatch) ){
                                $match = false;
                            }
                            else {
                                foreach( $this->wc->localisation->position as $i => $positionID ){
                                    if( !isset($positionMatch[$i-1]) ){
                                        break;
                                    }
                                    elseif( $positionMatch[$i-1] != $positionID )
                                    {
                                        $match = false;
                                        break;
                                    }
                                }   
                            }
                            break;
                            
                        default:
                            $message    =   "the context rule in configuration part: ";
                            $message    .=  $confPart." is not accepted by the system.";
                            $this->wc->log->error( $message );
                            break;
                    }
                    
                    if( $match )
                    {
                        $filename = "contexts/".$contextValues[$key];
                        $this->execFile = $this->wc->module->getControllerFile( $filename );
                        
                        if( $this->execFile ){
                            return $this->execFile;
                        }
                    }
                }
            }
        }
        
        return false;
    }
    
    
    function display()
    {
        include $this->getExecFile();
        
        return $this;
    }

}