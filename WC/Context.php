<?php
namespace WC;

class Context 
{
    const DEFAULT_FILE = "default";
    
    const DIR      = "contexts";
    
    const DESIGN_SUBFOLDER          = "design/contexts";
    const DESIGN_INCLUDES_SUBFOLDER = "design/contexts/includes";
    
    const IMAGES_SUBFOLDER          = "assets/images";
    const JS_SUBFOLDER              = "assets/js";
    const CSS_SUBFOLDER             = "assets/css";
    const FONTS_SUBFOLDER           = "assets/fonts";
    
    var $execFile;
    var $designFile;
    var $website;
    
    private $css    = [];
    private $js     = [];
    private $jsLib  = [];
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( Website $website, ?string $initialContext=null )
    {
        $this->website  = $website;
        $this->wc       = $this->website->wc;
        
        $context = $initialContext ?? self::DEFAULT_FILE;
        
        if( strcasecmp(substr($context, -4), ".php") == 0 ){
            $context = substr($context, 0, -4);
        }
        
        $this->execFile = $this->website->getFilePath( self::DIR."/".$context.".php" );

        if( !$this->execFile ){
            $this->wc->log->error("Context File: ".$context." not found");
        }
    }
    
    function setExecFile( string $context )
    {
        if( strcasecmp(substr($context, -4), ".php") == 0 ){
            $context = substr($context, 0, -4);
        }
        
        $this->execFile = $this->website->getFilePath( self::DIR."/".$context.".php" );

        if( !$this->execFile ){
            $this->wc->log->error("Context File: ".$context." not found");
        }
        
        return $this;
    }
    
    function getDesignFile( ?string $designFile=null, bool $mandatory=true )
    {
        if( $this->designFile ){
            return $this->designFile;
        }
        
        if( !$this->execFile ){
            return false;
        }
        
        if( !$designFile ){
            $designFile = basename( $this->execFile );
        }
        
        if( strcasecmp(substr($designFile, -4), ".php") == 0 ){
            $designFile = substr($designFile, 0, -4);
        }
        
        $this->designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/".$designFile.".php" );
        
        if( !$this->designFile ){
            $this->wc->log->error("Can't get design file: ".$designFile, $mandatory);
        }
        
        $this->wc->debug("Design file to be included : ".$this->designFile, 'CONTEXT');
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
    
    function display()
    {
        $this->wc->debug("Executing file: ".$this->execFile, 'CONTEXT');
        
        include $this->execFile;
        
        return $this;
    }

}