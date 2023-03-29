<?php
namespace WC\Attributes;

use WC\Attribute;

class ImageAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "image";
    const ELEMENTS          = [
        "file"      => "VARCHAR(511) DEFAULT NULL",
        "title"     => "VARCHAR(511) DEFAULT NULL",
    ];
    const PARAMETERS        = [];
    
    function __construct( $module, $attributeName, $params=[] )
    {
        $this->name         = $attributeName;
        
        parent::__construct( $module );
        
        $this->directory    = "files/".$this->type."/".$attributeName;
    }
    
    function setValue($key, $value)
    {
        if( $key == "file" && !empty($_FILES[ $this->name.'@'.$this->type.'#fileupload' ]["tmp_name"]) )
        {
            $tmpFileInfos = $_FILES[ $this->name.'@'.$this->type.'#fileupload' ];

            $check = getimagesize($tmpFileInfos["tmp_name"]);

            if( $check !== false )
            {
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
            
            return $this;
        }
        
        return parent::setValue($key, $value);
    }
    
    function set( $args )
    {
        if( !empty($_FILES[ $this->name.'@'.$this->type.'#fileupload' ][ "tmp_name" ]) )
        {
            $tmpFileInfos = $_FILES[ $this->name.'@'.$this->type.'#fileupload' ];
            
            $check = getimagesize($tmpFileInfos["tmp_name"]);

            if( $check !== false )
            {
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
        }
        
        if( isset($args['storeButton']) 
            && strcmp( $args['storeButton'], $this->name.'@'.$this->type.'#filedelete' ) == 0 
        ){
            $this->values['file'] = "";
        }
        
        parent::set($args);
        
        return true;
    }
    
    function content()
    {
        $filepath   = $this->getImageFile($this->values['file']);
        
        if( $filepath )
        {
            $content         = [];
            $content['file'] = $filepath;
            
            if( !empty($this->values['title']) ){
                $content['title'] = $this->values['title'];
            }
            else {
                $content['title'] = substr( $this->values['file'], 
                                            0, 
                                            strrpos($this->values['file'], ".") - strlen($this->values['file']) );
            }
            
            if( isset($this->values['link']) ){
                $content['link'] = $this->values['link'];
            }
            else {
                $content['link'] = false;
            }
            
            return $content;
        }
        else {
            return false;
        }
    }
    
    function getImageFile()
    {
        $filepath = $this->directory.'/'.$this->values['file'];
        
        if( !is_file($filepath) ){
            return false;
        }
        
        return "/".$filepath;
    }
}
