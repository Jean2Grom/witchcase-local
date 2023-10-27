<?php
namespace WC\Attribute;

class FileAttribute extends \WC\Attribute 
{
    const ATTRIBUTE_TYPE    = "file";
    const ELEMENTS          = [
        "file"      => "VARCHAR(511) DEFAULT NULL",
        "title"     => "VARCHAR(511) DEFAULT NULL",
    ];
    const PARAMETERS        = [];    
    
    var $directory;
    
    function __construct( \WC\WitchCase $wc, string $attributeName, array $params=[] )
    {
        parent::__construct( $wc, $attributeName, $params );
        
        $this->directory    = "files/".$this->type."/".$this->name;
    }
    
    function setValue($key, $value)
    {
        if( $key == "file" && !empty($_FILES[ $this->name.'@'.$this->type.'#fileupload' ]["tmp_name"]) )
        {
            $tmpFileInfos = $_FILES[ $this->name.'@'.$this->type.'#fileupload' ];
            
            if( filesize($tmpFileInfos["tmp_name"]) !== false 
                && copy($tmpFileInfos["tmp_name"], $this->newDirectoryName( $tmpFileInfos["name"] )) ){
                $this->values['file'] = $tmpFileInfos["name"];
            }
            
            return $this;
        }
        elseif( $key == "file" )
        {
            $localFile = $this->wc->request->param($this->name.'@'.$this->type.'#filemove');
            
            if( $localFile && is_file($localFile) )
            {
                $baseFilename = basename($localFile);
                
                if( copy($localFile, $this->newDirectoryName( $baseFilename )) ){
                    $this->values['file'] = $baseFilename;
                }
                
                return $this;                
            }
        }
        
        return parent::setValue($key, $value);
    }
    
    function content( ?string $element=null )
    {
        if( empty($this->values['file']) ){
            return null;
        }
        
        $filepath   = $this->getFile($this->values['file']);
        
        if( !$filepath ){
            return false;
        }
        
        if( $element == "file" || $element == "src" ){
            return $filepath;
        }
        
        $content         = [];
        $content['file'] = $filepath;

        if( !empty($this->values['title']) ){
            $content['title'] = $this->values['title'];
        }
        else {
            $content['title'] = "";
        }
        
        if( is_null($element) ){
            return $content;
        }
        
        return $content[ $element ] ?? null;
    }
    
    function getFile()
    {
        $filepath = $this->directory.'/'.$this->values['file'];
        
        if( !is_file($filepath) ){
            return false;
        }
        
        return "/".$filepath;
    }
    
    private function newDirectoryName( string $baseFilename ): string
    {
        $directoryPath = "";
        foreach( explode('/', $this->directory) as $folder ) 
        {
            $directoryPath .= $folder;
            if( !is_dir($directoryPath) ){
                mkdir( $directoryPath, 0705 );                          
            }

            $directoryPath .= "/";
        }
        
        return $directoryPath.$baseFilename;
    }
}
