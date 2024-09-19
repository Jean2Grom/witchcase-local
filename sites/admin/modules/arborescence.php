<?php /** @var WC\Module $this */

use WC\Handler\WitchHandler;

$currentId = $this->witch("target")?->id ?? $this->witch()?->id;

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
            return $witch->url();
        }
        
        return $this->baseUrl.'?id='.$witch->id;
    }
};

$obj->baseUrl       = $this->wc->website->getUrl("view");
$obj->unSafeMode    = $this->config['navigationUnSafeMode'] ?? false;
$obj->currentSite   = $this->wc->website->site;

$root   = WitchHandler::recursiveTree( $this->witch, $this->wc->website->sitesRestrictions, $currentId, $this->maxStatus, [$obj, "href"] );

$tree       = [ $this->witch->id => $root ];
$breadcrumb = [ $this->witch->id ];
$pathFound  = true;
$daughters  = $root["daughters"];
$draggble   = true;

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
