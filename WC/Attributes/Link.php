<?php

namespace WC\Attributes;

use WC\Attribute;

class Link extends Attribute {
    
    function __construct( $module, $attributeName, $params=[] )
    {
        parent::__construct( $module );
        
        $this->type     = "link";
        $this->name     = $attributeName;
        
        $this->tableColumns =   [
                                    "href"      =>  "@_link#href__".$attributeName, 
                                    "text"      =>  "@_link#text__".$attributeName, 
                                    "external"  =>  "@_link#external__".$attributeName, 
                                ];
        $this->values       =   [
                                    "href"      =>  "",
                                    "text"      =>  "",
                                    "external"  =>  1,
                                ];
        
        $this->dbFields     =   [
                                    "`@_link#href__".
                                        $attributeName.
                                        "` varchar(511) DEFAULT NULL",
                                    "`@_link#text__".
                                        $attributeName.
                                        "` varchar(511) DEFAULT NULL",
                                    "`@_link#external__".
                                        $attributeName.
                                        "` TINYINT(1) DEFAULT 1",
                                ];
    }
    
    function content()
    {
        if( !empty($this->values['href']) )
        {
            $content         = [];
            $content['href'] = $this->values['href'];
            
            if( !empty($this->values['text']) )
            {   $content['text'] = $this->values['text'];   }
            else
            {   $content['text'] = $content['href'];    }
            
            if( $this->values['external'] )
            {   $content['external'] = true;    }
            else 
            {   $content['external'] = false;   }
            
            return $content;
        }
        else
        {   return false;   }
    }

    
}
