<?php
$this->addJsLibFile('jquery-3.6.0.min.js');
$this->addJsFile('fontawesome.js');
$this->addCssFile('arborescence_menu.css');
$this->addJsFile('arborescence_menu.js');

$currentWitch   = $this->wc->website->witches["current"];

$currentId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if( !$currentId ){
    $currentId      = $currentWitch->id;
}

$root = recursiveTree( $this, $this->witch, $this->wc->website, $currentId );
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

function recursiveTree( WC\Module $module, \WC\Witch $witch, $website, $currentId=false )
{
    if( !is_null($witch->site) 
        && $website->sitesRestrictions 
        && !in_array($witch->site, $website->sitesRestrictions) ){
        return false;
    }
    
    $path       = false;
    if( $currentId && $currentId == $witch->id ){
        $path = true;
    }
    
    $daughters  = [];
    foreach( $module->getDaughters($witch) as $daughterWitch )
    {
        $subTree        = recursiveTree( $module, $daughterWitch, $website, $currentId );
        if( $subTree === false ){
            continue;
        }
        
        if( $subTree['path'] ){
            $path = true;
        }
        
        $daughters[ $subTree['id'] ]    = $subTree;
    }
    
    $uri = $website->baseUri;
    if( $witch->uri != $uri || $website->name != $witch->site ){
        if( substr($uri, -1) !== '/'){
            $uri .= '/';            
        }
        $uri .= "view?id=".$witch->id;
    }
    
    $tree   = [ 
        'id'                => $witch->id,
        'uri'               => $uri,
        'name'              => $witch->name,
        'site'              => $witch->site ?? "",
        'description'       => $witch->data,
        'craft'             => $witch->hasTarget(),
        'invoke'            => !empty($witch->invoke),
        'daughters'         => $daughters,
        'daughters_orders'  => array_keys( $daughters ),
        'path'              => $path,
    ];
    
    return $tree;
}

include $this->getDesignFile();
