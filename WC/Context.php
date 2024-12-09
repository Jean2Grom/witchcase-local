<?php
namespace WC;

use WC\Trait\ShortcutAccessTrait;

/**
 * Layout class that handle display
 * 
 * @author Jean2Grom
 */
class Context 
{
    use ShortcutAccessTrait;

    const DEFAULT_FILE  = "default";    
    const DIR           = "contexts";
    
    const DESIGN_SUBFOLDER          = "design/contexts";
    const DESIGN_INCLUDES_SUBFOLDER = "design/contexts/includes";
    
    const IMAGES_SUBFOLDER          = "assets/images";
    const JS_SUBFOLDER              = "assets/js";
    const CSS_SUBFOLDER             = "assets/css";
    const FONTS_SUBFOLDER           = "assets/fonts";
    
    const CSS_FILE_DISPLAY          = "css-file.php";
    const JS_FILE_DISPLAY           = "js-file.php";
    const IMAGE_FILE_DISPLAY        = "image-file.php";
    const FAVICON_FILE_DISPLAY      = "favicon-file.php";
    
    public $name;
    public $execFile;
    public $designFile;
    public $website;
    public $view;
    
    private $css    = [];
    private $js     = [];
    private $jsLib  = [];
    
    private $customVars  = [];
    
    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;
    
    function __construct( Website $website, ?string $initialContext=null )
    {
        $this->website  = $website;
        $this->wc       = $this->website->wc;
        
        $this->name     = $initialContext ?? self::DEFAULT_FILE;
        
        if( strcasecmp(substr( $this->name, -4), ".php") == 0 ){
             $this->name = substr( $this->name, 0, -4);
        }
        
        if( empty($this->name) ){
            $this->wc->log->error("Context implemented with empty initilialisation");
        }
    }
    
    function set( string $context )
    {
        if( strcasecmp(substr($context, -4), ".php") == 0 ){
            $context = substr($context, 0, -4);
        }
        
        $this->name     = $context;
        
        if( empty($this->name) ){
            $this->wc->log->error("Context has been set with empty value");
        }
        
        return $this;
    }
    
    function getDesignFile( ?string $designFile=null, bool $mandatory=true )
    {
        if( $this->designFile ){
            return $this->designFile;
        }
        
        if( !$designFile ){
            $designFile = $this->name;
        }
        
        if( strcasecmp(substr($designFile, -4), ".php") == 0 ){
            $designFile = substr($designFile, 0, -4);
        }
        
        $this->designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/".$designFile.".php" );
        
        if( !$this->designFile ){
            $this->wc->log->error("Can't get design file: ".$designFile, $mandatory);
        }
        
        $this->wc->debug->toResume("Design file to be included : \"".$this->designFile."\"", 'CONTEXT');
        return $this->designFile;
    }
    
    function view( ?string $designName=null, bool $mandatory=true )
    {
        $this->view = true;        
        return $this->getDesignFile( $designName, $mandatory );
    }
    
    
    /**
     * Access internal css file ressource that can be access from web browser,  
     * Resolving fallbacks and returning relative webpath
     * 
     * @param string $cssFile 
     * @return string|null
     */
    function cssSrc( string $cssFile ): ?string {
        return $this->wc->website->getWebPath( self::CSS_SUBFOLDER."/".$cssFile );
    }
    
    /**
     * Display css file 
     * 
     * @param string $cssFile
     * @param array $attributes
     * @return void
     */
    function css( string $cssFile, array $attributes=[] ): void
    {
        $displayFilePath    = $this->wc->website->getFilePath( self::DESIGN_INCLUDES_SUBFOLDER."/".self::CSS_FILE_DISPLAY );
        if( empty($displayFilePath) )
        {
            $this->wc->log->error("Can't get CSS file display file");
            return;
        }
        
        $cssSrc = $this->cssSrc( $cssFile );
        if( empty($cssSrc) )
        {
            $this->wc->log->error("Can't get CSS file");
            return;
        }
        
        include $displayFilePath;
        
        return;
    }
    
    function addCssFile( string $cssFile ): bool
    {
        $cssWebPath = $this->cssSrc( $cssFile );
        
        if( $cssWebPath 
            && !in_array($cssWebPath, $this->css)
        ){
            $this->css[] = $cssWebPath;
        }
        else {
            return false;
        }
        
        return true;
    }
    
    function getCssFiles(): array {
        return $this->css;
    }
    
    function js( string $jsFile, array $attributes=[] ): void
    {
        $displayFilePath    = $this->wc->website->getFilePath( self::DESIGN_INCLUDES_SUBFOLDER."/".self::JS_FILE_DISPLAY );
        if( empty($displayFilePath) )
        {
            $this->wc->log->error("Can't get JS file display file");
            return;
        }
        
        $jsSrc = $this->jsSrc( $jsFile );
        if( empty($jsSrc) )
        {
            $this->wc->log->error("Can't get JS file");
            return;
        }
        
        include $displayFilePath;
        
        return;
    }
    
    function jsSrc( string $jsFile ): ?string {
        return $this->wc->website->getWebPath( self::JS_SUBFOLDER."/".$jsFile );
    }
    
    function addJsFile( string $jsFile ): bool
    {
        $jsWebPath = $this->jsSrc( $jsFile );
        
        if( $jsWebPath 
            && !in_array($jsWebPath, $this->js) 
        ){
            $this->js[] = $jsWebPath;
        }
        else {
            return false;
        }
        
        return true;
    }
    
    function getJsFiles(): array {
        return $this->js;
    }
    
    function addJsLibFile( string $jsFile ): bool
    {
        $jsWebPath = $this->wc->website->getWebPath( self::JS_SUBFOLDER."/".$jsFile );
        
        if( $jsWebPath 
            && !in_array($jsWebPath, $this->jsLib) 
        ){
            $this->jsLib[] = $jsWebPath;
        }
        else {
            return false;
        }
        
        return true;
    }
    
    function getJsLibFiles(): array {
        return $this->jsLib;
    }
    
    function imageSrc( string $imageFile ): ?string {
        return $this->wc->website->getWebPath( self::IMAGES_SUBFOLDER."/".$imageFile );
    }
    
    function image( string $imageFile, array $attributes=[] ): void
    {
        $displayFilePath    = $this->wc->website->getFilePath( self::DESIGN_INCLUDES_SUBFOLDER."/".self::IMAGE_FILE_DISPLAY );
        if( empty($displayFilePath) )
        {
            $this->wc->log->error("Can't get IMAGE file display file");
            return;
        }
        
        $imageSrc = $this->imageSrc( $imageFile );
        if( empty($imageSrc) )
        {
            $this->wc->log->error("Can't get IMAGE file");
            return;
        }
        
        include $displayFilePath;
        
        return;
    }
    
    function getImageFile( string $imageFile ): ?string {
        return $this->imageSrc( $imageFile );
    }
    
    function favicon( string $iconFile="favicon.ico" ): void
    {
        $displayFilePath    = $this->wc->website->getFilePath( self::DESIGN_INCLUDES_SUBFOLDER."/".self::FAVICON_FILE_DISPLAY );
        if( empty($displayFilePath) )
        {
            $this->wc->log->error("Can't get FAVICON file display file");
            return;
        }
        
        $iconSrc = $this->imageSrc( $iconFile );
        if( empty($iconSrc) )
        {
            $this->wc->log->error("Can't get FAVICON file");
            return;
        }
        
        $iconMime    = mime_content_type( $this->wc->website->getFilePath(self::IMAGES_SUBFOLDER."/".$iconFile) );
        
        include $displayFilePath;
        
        return;
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
        
        $this->wc->debug->toResume("Ressource design file to be Included: \"".$fullPath."\"", 'CONTEXT');
        return $fullPath;
    }
    
    function display()
    {
        $this->execFile = $this->website->getFilePath( self::DIR."/". $this->name.".php" );
        
        if( !$this->execFile )
        {
            $this->wc->debug->toResume("Context File: \"". $this->name."\" not found, searching for ".self::DEFAULT_FILE." file", 'CONTEXT');
            $this->execFile = $this->website->getFilePath( self::DIR."/".self::DEFAULT_FILE.".php" );
        }
        
        if( !$this->execFile ){
            $this->wc->log->error("Context File: ". $this->name." not found", true);
        }        
        
        $this->wc->debug->toResume("Executing file: \"".$this->execFile."\"", 'CONTEXT');
        
        include $this->execFile;
        if( $this->view ){
            include $this->getDesignFile();
        }
        
        return $this;
    }
    
    function addVar( string $name, mixed $value ): void {
        $this->customVars[ $name ] = $value;
    }
    
    function addArrayItems( string $arrayName, array $values ): void {
        $this->customVars[ $arrayName ] = array_replace($this->customVars[ $arrayName ] ?? [], $values);
    }
    
    function getVar( string $name ): mixed {
        return $this->customVars[ $name ] ?? null;
    }
    
    function __get( string $name ): mixed {
        return $this->getVar($name);
    }
}