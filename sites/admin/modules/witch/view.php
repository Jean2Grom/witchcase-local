<?php 
/** 
 * @var WC\Module $this 
 */

//use WC\Handler\CauldronHandler;

use WC\Handler\CauldronHandler;
use WC\Structure;
use WC\Tools;
use WC\Website;

$action = Tools::filterAction(
    $this->wc->request->param('action'),
    [
        'remove-cauldron',
        'create-cauldron',
    ], 
);
$this->wc->debug($action);


if( !$this->witch("target") ){
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Witch not found"
    ]);
    
    header('Location: '.$this->wc->website->getRootUrl() );
    exit();
}



if( $this->witch("target")->hasCauldron() )
{
    //$result = CauldronHandler::fetch($this->wc, [ $this->witch("target")->cauldron ]);
    //$structures     = $this->wc->configuration->structures();
    //$ingredients    = WC\Ingredient::list();
    
    //$this->wc->debug( $this->witch("target")->cauldron() );
    $this->wc->debug( $this->wc->configuration->structures() );
}
/*
$this->wc->debug( 
    $this->wc->configuration->structure(
        $this->wc->request->param('witch-cauldron-structure')
    )
);
*/

$this->wc->debug( $this->wc->request->inputs() );

switch( $action )
{
    case 'remove-cauldron':
        if( !$this->witch("target")->cauldron() ){
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, cauldron wasn't found",
            ]);
        }
        else if( !$this->witch("target")->removeCauldron() ){
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error occurred, cauldron removal was canceled",
            ]);
        }
        else {
            $this->wc->user->addAlert([
                'level'     =>  'success',
                'message'   =>  "Cauldron removed"
            ]);
        }
    break;

    case 'create-cauldron':

        $cauldron = CauldronHandler::createFromData($this->wc, [
            'name'      =>  "NEW CAULDRON",
            'data'      =>  json_encode([ 'structure' => $this->wc->request->param('witch-cauldron-structure') ?? "folder" ]),
        ]);

        $this->wc->dump($cauldron);
    break;
}

$structuresList = [];
$craftWitches   = null;
if( !$this->witch("target")->hasCraft() ){
    $structuresList = Structure::listStructures( $this->wc );
}
else 
{
    $craftWitches = $this->witch("target")->craft()->getWitches();
    
    foreach( $craftWitches as $key => $craftWitch )
    {
        $breadcrumb         = [];
        $breadcrumbWitch    = $craftWitch->mother();
        while( !empty($breadcrumbWitch) )
        {
            $breadcrumb[] = [
                "name"  => $breadcrumbWitch->name,
                "data"  => $breadcrumbWitch->data,
                "href"  => $this->witch->url([ 'id' => $breadcrumbWitch->id ]),
            ];

            $breadcrumbWitch = $breadcrumbWitch->mother();
        }
        
        $craftWitches[ $key ]->breadcrumb = array_reverse($breadcrumb);
    }
    
    $craftWitchesTargetFirst    = [];
    $craftWitchesTargetFirst[]  = $craftWitches[ $this->witch("target")->id ];
    foreach( $craftWitches as $key => $craftWitch ){
        if( $key !=  $this->witch("target")->id ){
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

$breadcrumb         = [];
$breadcrumbWitch    = $this->witch("target");
while( !empty($breadcrumbWitch) )
{
    if( $breadcrumbWitch  === $this->witch("target") ){
        $url    = "javascript: location.reload();";
    }
    else {
        $url    = $this->witch->url([ 'id' => $breadcrumbWitch->id ]);
    }
    
    $breadcrumb[]   = [
        "name"  => $breadcrumbWitch->name,
        "data"  => $breadcrumbWitch->data,
        "href"  => $url,
    ];

    if( $this->witch('root') === $breadcrumbWitch ){
        break;
    }
    
    $breadcrumbWitch    = $breadcrumbWitch->mother();    
}

$this->addContextVar( 'breadcrumb', array_reverse($breadcrumb) );

$targetWitch    = $this->witch("target");

$this->view();