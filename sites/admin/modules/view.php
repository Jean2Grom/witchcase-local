<?php
use WC\Structure;
use WC\Website;

$targetWitch = $this->wc->witch("target");

if( !$targetWitch->exist() ){
    $alert = [
        'level'     =>  'error',
        'message'   =>  "Witch not found"
    ];
    $this->wc->user->addAlerts([ $alert ]);
    
    header('Location: '.$this->wc->website->getRootUrl() );
    exit();
}

$structuresList = [];
$craftWitches    = null;
if( !$targetWitch->hasCraft() ){
    $structuresList = Structure::listStructures( $this->wc );
}
else 
{
    $craftWitches = $targetWitch->craft()->getWitches();
    
    foreach( $craftWitches as $key => $craftWitch )
    {
        $breadcrumb = [];
        $breadcrumbWitch    = $craftWitch->mother();
        while( !empty($breadcrumbWitch) )
        {
            $breadcrumb[]   = [
                "name"  => $breadcrumbWitch->name,
                "data"  => $breadcrumbWitch->data,
                "href"  => $this->witch->getUrl([ 'id' => $breadcrumbWitch->id ]),
            ];

            $breadcrumbWitch    = $breadcrumbWitch->mother();
        }
        
        $craftWitches[ $key ]->breadcrumb = array_reverse($breadcrumb);
    }
    
    $craftWitchesTargetFirst = [];
    $craftWitchesTargetFirst[] = $craftWitches[ $targetWitch->id ];
    foreach( $craftWitches as $key => $craftWitch ){
        if( $key !=  $targetWitch->id ){
            $craftWitchesTargetFirst[] = $craftWitch;
        }
    }
}

$sites  = $this->wc->website->sitesRestrictions;
if( !$sites ){
    $sites = array_keys($this->wc->configuration->sites);
}

$websitesList   = [];
foreach( $sites as $site ){
    if( $site == $this->wc->website->name ){
        $website = $this->wc->website;
    }
    else {
        $website = new Website( $this->wc, $site );
    }
    
    if( $website->site == $website->name ) {
        $websitesList[ $site ] = $website;
    }
}

$breadcrumb = [
    [
        "name"  => $targetWitch->name,
        "data"  => $targetWitch->data,
        "href"  => $this->witch->getUrl([ 'id' => $targetWitch->id ]),
    ]
];

$breadcrumbWitch    = $targetWitch->mother();
while( !empty($breadcrumbWitch) )
{
    $breadcrumb[]   = [
        "name"  => $breadcrumbWitch->name,
        "data"  => $breadcrumbWitch->data,
        "href"  => $this->witch->getUrl([ 'id' => $breadcrumbWitch->id ]),
    ];
    
    $breadcrumbWitch    = $breadcrumbWitch->mother();
}

$this->addContextVar( 'breadcrumb', array_reverse($breadcrumb) );

$this->view();