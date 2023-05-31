<?php

$currentId = $this->wc->request->param("id", "get", FILTER_VALIDATE_INT, 0) ?? $this->wc->witch()->id;

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
    
    $tree   = [ 
        'id'                => $witch->id,
        'uri'               => $website->getUrl("view?id=".$witch->id),
        'name'              => $witch->name,
        'site'              => $witch->site ?? "",
        'description'       => $witch->data,
        'craft'             => $witch->hasCraft(),
        'invoke'            => $witch->hasInvoke(),
        'daughters'         => $daughters,
        'daughters_orders'  => array_keys( $daughters ),
        'path'              => $path,
    ];
    
    return $tree;
}

$this->view();
