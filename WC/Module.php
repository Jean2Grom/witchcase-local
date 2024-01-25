<?php
namespace WC;

/**
 * Class dedicated to module invokation (execution)
 * and display insertion in a Context class
 * 
 * @author Jean2Grom
 */
class Module 
{
    const DEFAULT_FILE  = "default";   
    const DIR           = "modules";
    
    const DESIGN_SUBFOLDER              = "design/modules";
    const DESIGN_INCLUDES_SUBFOLDER     = "design/modules/includes";
    
    public $name;
    public $execFile;
    public $designFile;
    public $result;
    public $config;
    public $view;
    public $maxStatus;
    public $isRedirection;
    public $allowContextSetting;
    
    /**
     * Witch that calls this module
     * @var Witch
     */
    public Witch $witch;
    
    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;
    
    function __construct( Witch $witch, string $moduleName )
    {
        $this->witch    = $witch;
        $this->wc       = $this->witch->wc;
        
        if( strcasecmp(substr($moduleName, -4), ".php") == 0 ){
            $moduleName =  substr($moduleName, 0, -4);
        }        
        
        $this->name     = $moduleName;
        $this->execFile = $this->wc->website->getFilePath( self::DIR.'/'.$this->name.".php" );
        
        if( !$this->execFile ){
            $this->execFile = $this->wc->website->getFilePath( self::DIR."/".self::DEFAULT_FILE.".php" );
        }
        
        $this->config = array_replace_recursive( 
                            $this->wc->website->modules['*'] ?? [],
                            $this->wc->website->modules[ $this->name ] ?? []
                        );
        
        $this->maxStatus = 0;
        foreach( $this->wc->user->policies as $policy ){
            if( $policy["module"] == $this->name || $policy["module"] == '*' ){
                if( $policy["status"] == '*' ){
                    $this->maxStatus = false;
                }
                elseif( $this->maxStatus !== false && $policy["status"] > $this->maxStatus ){
                    $this->maxStatus = (int) $policy["status"];
                }
            }
        }
    }
    
    function execute()
    {
        if( !$this->isValid() ){
            $this->wc->log->error("Cannot execute unvalid module : ".$this->name, true);
        }
        
        if( !empty($this->config['defaultContext']) ){
            $this->setContext($this->config['defaultContext']);
        }
        
        $this->wc->debug->toResume("Executing file: \"".$this->execFile."\"", 'MODULE '.$this->name);
        ob_start();
        include $this->execFile;        
        if( $this->view ){
            include $this->getDesignFile();
        }
        $result = ob_get_contents();
        ob_end_clean();
        
        $this->result = $result;
        
        return $this->result;
    }
    
    function isValid(): bool
    {
        if( !$this->execFile )
        {
            $this->wc->log->error("Can't access module file: ".$this->name);
            return false;
        }

        return true;
    }
    
    
    function setResult( $result )
    {
        $this->result = $result;
        return $this;
    }

    function getResult(){
        return $this->result;
    }
    
    function getDesignFile( ?string $designName=null, bool $mandatory=true )
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
        
        $this->wc->debug->toResume("Design file to be included : \"".$this->designFile."\"", 'MODULE '.$this->name);
        return $this->designFile;
    }
    
    function view( ?string $designName=null, bool $mandatory=true )
    {
        $this->view = true;        
        return $this->getDesignFile( $designName, $mandatory );
    }
    
    function getImageFile( $filename ){
        return $this->wc->website->context->imageSrc( $filename );
    }
    
    function image( string $filename ): ?string {
        return $this->wc->website->context->imageSrc( $filename );
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
    
    function setContext( $context )
    {
        if( $this->allowContextSetting ){
            return $this->wc->website->context->set( $context );
        }
        
        return false;
    }
    
    function getIncludeDesignFile( $filename )
    {
        $fullPath = $this->wc->website->getFilePath(self::DESIGN_INCLUDES_SUBFOLDER."/".$filename );
        
        if( !$fullPath ){
            return false;
        }
        
        $this->wc->debug->toResume("Ressource design file to be Included: \"".$fullPath."\"", 'MODULE '.$this->name);
        return $fullPath;
    }
    
    function getDaughters( Witch $witch=null )
    {
        if( empty($witch) ){
            $witch = $this->witch;
        }
        
        $daughters = [];
        foreach( $witch->daughters() as $daughterKey => $daughterWitch ){
            if( $this->maxStatus === false || $daughterWitch->statusLevel <= $this->maxStatus ){
                $daughters[ $daughterKey ] = $daughterWitch;
            }
        }
        
        return $daughters;
    }
    
    function setIsRedirection( bool $isRedirection ): self
    {
        $this->isRedirection = $isRedirection;
        
        return $this;
    }
    
    function setAllowContextSetting( bool $allowContextSetting ): self
    {
        $this->allowContextSetting = $allowContextSetting;
        
        return $this;
    }
    
    
    
    function addContextVar( string $name, mixed $value ){
        return $this->wc->website->context->addVar( $name, $value );
    }
    
    function addContextArrayItems( string $arrayName, mixed $itemValue )
    {
        if( !is_array($itemValue) ){
            $value = [ $itemValue ];
        }
        else {
            $value = $itemValue;
        }
        
        return $this->wc->website->context->addArrayItems( $arrayName, $value );
    }
}