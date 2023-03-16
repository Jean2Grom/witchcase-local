<?php

$backgroundImage    = $this->target->attributes['background-image']->content()['file'];
$headline           = $this->target->attributes['headline']->content();
$headlineBody       = $this->target->attributes['body']->content();

if( !empty($this->target->attributes["contents"]) )
{
    $contents = $this->target->attributes["contents"]->content();
    
    $downloads  = [];
    $images     = [];
    $bodies     = [];
    $columns    = [];
    $links      = [];
    foreach( $contents as $content )
    {
        $this->debug->dump($content);
        
        if( isset($content["attributes"]["download"]) )
        {   $downloads[]= $content["attributes"]["download"]->content();    }
        else
        {   $downloads[]= false;    }
        
        if( isset($content["attributes"]["image"]) )
        {   $images[]   = $content["attributes"]["image"]->content();   }
        else
        {   $images[]   = false;    }
        
        if( isset($content["attributes"]["body"]) )
        {   $bodies[]   = $content["attributes"]["body"]->content();    }
        else
        {   $bodies[]   = false;    }
        
        if( isset($content["attributes"]["visit"]) )
        {   $links[]    = $content["attributes"]["visit"]->content();   }
        else
        {   $links[]    = false;    }
        
        if( isset($content["attributes"]["left-column"]) 
            && isset($content["attributes"]["center-column"]) 
            && isset($content["attributes"]["right-column"]) 
        ){
            $columns[]  =   [
                'left'      =>  $content["attributes"]["left-column"]->content(), 
                'center'    =>  $content["attributes"]["center-column"]->content(), 
                'right'     =>  $content["attributes"]["right-column"]->content()
            ];
        }
        else
        {
            $columns[]  =   [
                'left'      =>  false, 
                'center'    =>  false, 
                'right'     =>  false,
            ];
        }
    }
    
}
include $this->module->getDesignFile();


