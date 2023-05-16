<?php

$baseUri    = "https://".$this->localisation->siteAccess;
$folder     = "contexts/".$this->localisation->site;

$rootLocalisation   = false;
$rootContent        = false;
if( $this->localisation->id == $this->configuration->read($this->localisation->site, 'rootID') )
{
    $rootLocalisation   = $this->localisation;
    $rootContent        = $this->craft();
}

//menuPart
$menu = $this->wc->cache->read( $folder, 'menu' );

if( empty($menu) )
{
    if( !$rootLocalisation ){
        $rootLocalisation = new Localisation( $this->configuration, $this->db, $this->configuration->read($this->localisation->site, 'rootID') );
    }
        
    $menu = [];
    foreach(  $rootLocalisation->children() as $child )
    {
        $menu[] =   [
            'name'  =>  $child->name,
            'url'   =>  $baseUri.$child->url,
        ];
    }
    
    $this->cache->create($folder, 'menu', $menu );
}


// context data
$contextData = $this->wc->cache->read( $folder, 'contextData' ) ?? [];
if( $cache 
    && !$rootContent
){
    include $cache;
}
if( empty($contextData) )
{
    if( !$rootContent )
    {
        if( !$rootLocalisation ){
            $rootLocalisation = new Localisation( $this->configuration, $this->db, $this->configuration->read($this->localisation->site, 'rootID') );
        }
        $rootContent        = $rootLocalisation->getTarget();
    }
    
    $contextData['meta-title']          = $rootContent->attributes['meta-title']->content();
    $contextData['meta-description']    = $rootContent->attributes['meta-description']->content();
    $contextData['meta-keywords']       = $rootContent->attributes['meta-keywords']->content();
    $contextData['logo']                = $rootContent->attributes['logo']->content();
    $contextData['contact-email']       = $rootContent->attributes['contact-email']->content();
    $contextData['download-highlight']  = $rootContent->attributes['download-highlight']->content();
    $contextData['footer-left']         = $rootContent->attributes['footer-left']->content();
    $contextData['footer-right']        = $rootContent->attributes['footer-right']->content();
    
    $this->cache->create($folder, 'contextData', $contextData );    
}

include $context->getDesignFile();