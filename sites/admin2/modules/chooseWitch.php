<?php
use WC\Witch;

$currentId  = $this->wc->request->param("id", "get", FILTER_VALIDATE_INT) ?? $this->wc->witch()->id;

//$website->getUrl("view?id=".$witch->id);
//$obj = new stdClass();
$obj = new class {
    public $baseUrl;

    public function href( $witch ) { 
        return $this->baseUrl.$witch->id;
    }
};

$obj->baseUrl = $this->wc->website->getUrl("view?id=");
        
        
$root       = Witch::recursiveTree( $this->witch, $this->wc->website->sitesRestrictions, $currentId, $this->maxStatus, [$obj, "href"] );

$tree = [ $this->witch->id => $root ];

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

$this->view('arborescence');
