<?php
use WC\Witch;

$currentId = $this->wc->request->param("id", "get", FILTER_VALIDATE_INT) ?? $this->wc->witch()->id;

$obj = new class {
    public $baseUrl;
    public $unSafeMode;
    public $currentSite;

    public function href( $witch ) 
    {
        if( !$this->unSafeMode 
            && $witch->invoke 
            && $witch->site === $this->currentSite 
        ){
            return $witch->getUrl();
        }
        
        return $this->baseUrl.'?id='.$witch->id;
    }
};

$obj->baseUrl       = $this->wc->website->getUrl("view");
$obj->unSafeMode    = $this->config['navigationUnSafeMode'] ?? true;
$obj->currentSite   = $this->wc->website->site;
        
$root   = Witch::recursiveTree( $this->witch, $this->wc->website->sitesRestrictions, $currentId, $this->maxStatus, [$obj, "href"] );

$tree       = [ $this->witch->id => $root ];
$breadcrumb = [ $this->witch->id ];
$pathFound  = true;
$daughters  = $root["daughters"];

while( $pathFound )
{
    $pathFound  = false;
    
    foreach( $daughters as $daughterWitch ){
        if( $daughterWitch['path'] )
        {
            $pathFound      = true;
            $breadcrumb[]   = $daughterWitch['id'];
            $daughters      = $daughterWitch['daughters'];
            break;
        }
    }
}

$this->view();
