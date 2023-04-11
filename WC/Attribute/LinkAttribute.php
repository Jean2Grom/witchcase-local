<?php
namespace WC\Attribute;

use WC\Attribute;
use WC\WitchCase;

class LinkAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "link";
    const ELEMENTS          = [
        "href"          => "VARCHAR(511) DEFAULT NULL",
        "text"          => "VARCHAR(511) DEFAULT NULL",
        "external"      => "TINYINT(1) DEFAULT 1",
    ];
    const PARAMETERS        = [];
    
    
    function __construct( WitchCase $wc, string $attributeName, array $params=[] )
    {
        parent::__construct( $wc, $attributeName, $params );
        
        $this->values = [
            "href"      =>  "",
            "text"      =>  "",
            "external"  =>  1,
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
