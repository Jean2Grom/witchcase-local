<?php
namespace WC\Attribute;

class FileAttribute extends \WC\Attribute 
{
    const ATTRIBUTE_TYPE    = "file";
    const ELEMENTS          = [
        "filename"  => "VARCHAR(511) DEFAULT NULL",
        "text"      => "VARCHAR(511) DEFAULT NULL",
    ];
    const PARAMETERS        = [];
    
    
    function __construct( \WC\WitchCase $wc, string $attributeName, array $params=[] )
    {
        parent::__construct( $wc, $attributeName, $params );
        
        $this->directory    = "files/".$this->type."/".$this->name;
        
        $this->dbFields     =   [
            "file"  =>  "`@_".$this->type."#file__".$this->name."` varchar(511) DEFAULT NULL",
            "text"  =>  "`@_".$this->type."#text__".$this->name."` varchar(511) DEFAULT NULL",
        ];
        
        $this->tableColumns =   [
                                    "file"  =>  "@_".$this->type."#file__".$this->name,
                                    "text"  =>  "@_".$this->type."#text__".$this->name,
                                ];
        $this->values       =   [
                                    "file"  =>  "",
                                    "text"  =>  "",
                                ];
        $this->parameters   =   [];
    }
    
    function set( $args )
    {
        if( !empty($_FILES['@_'.$this->type.'#fileupload__'.$this->name]["tmp_name"]) )
        {
            $tmpFileInfos = $_FILES['@_'.$this->type.'#fileupload__'.$this->name];
            
            $directoryPath = "";
            foreach( explode('/', $this->directory) as $folder ) 
            {
                $directoryPath .= $folder;
                if( !is_dir($directoryPath) ) 
                {   mkdir( $directoryPath, 0705 );  }

                $directoryPath .= "/";
            }

            if( copy($tmpFileInfos["tmp_name"], $directoryPath.$tmpFileInfos["name"]) ){
                $this->values['file'] = $tmpFileInfos["name"];
            }
        }
        
        if( isset($args['storeButton']) 
            && strcmp($args['storeButton'], '@_'.$this->type.'#filedelete__'.$this->name) == 0 
        ){
            $this->values['file'] = "";
        }
        
        parent::set($args);
        
        return true;
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
        
        if( $element == "file" ){
            return $filepath;
        }
        
        $content         = [];
        $content['file'] = $filepath;
        
        if( !empty($this->values['text']) ){
            $content['text'] = $this->values['text'];
        }
        else {
            $content['text'] =  substr( $this->values['file'], 
                                        0, 
                                        strrpos($this->values['file'], ".") - strlen($this->values['file']) 
                                );
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
        
        return $this->module->getHost()."/".$filepath;
    }
}
