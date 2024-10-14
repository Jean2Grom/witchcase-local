<?php 
/** 
 * @var WC\Module $this 
 */

use WC\Handler\CauldronHandler;
use WC\Handler\WitchHandler;
use WC\Structure;
use WC\Tools;
use WC\Website;


if( !$this->witch("target") ){
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Witch not found"
    ]);
    
    header('Location: '.$this->wc->website->getRootUrl() );
    exit();
}

/*
if( $this->witch("target")->hasCauldron() )
{
    //$result = CauldronHandler::fetch($this->wc, [ $this->witch("target")->cauldron ]);
    //$structures     = $this->wc->configuration->structures();
    //$ingredients    = WC\Ingredient::list();
    
    //$this->wc->debug( $this->witch("target")->cauldron() );
    $this->wc->debug( $this->wc->configuration->structures() );
}
*/

$this->wc->debug( $this->wc->request->inputs() );

switch( Tools::filterAction( 
    $this->wc->request->param('action'),
    [
        'remove-cauldron',
        'create-cauldron',
        'import-cauldron',
    ]
) ){
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
        $structure      = $this->wc->request->param('witch-cauldron-structure') ?? "folder";
        if( !in_array($structure, $this->wc->configuration->structures()) )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, a valid cauldron structure is missing",
            ]);
            break;
        }         

        $folderCauldron = CauldronHandler::getStorageStructure($this->wc, $this->wc->website->site, $structure);
        if( !$folderCauldron )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, cauldron storage structure can't be found",
            ]);
            break;
        }

        $cauldron = CauldronHandler::createFromData($this->wc, [
            'name'      =>  $this->witch("target")->name,
            'data'      =>  json_encode([ 'structure' => $structure ]),
        ]);

        if( !$cauldron 
            || !$folderCauldron->addCauldron( $cauldron ) 
            || !$cauldron->save() 
            || !$this->witch("target")->edit([ 'cauldron' => $cauldron->id ]) 
        ){
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error occurred during cauldron creation",
            ]);
            break;
        }

        $this->wc->user->addAlert([
            'level'     =>  'success',
            'message'   =>  "Cauldron was created",
        ]);

        header('Location: '.$this->wc->website->getUrl("cauldron?id=".$this->witch("target")->id) );
        exit();    
    break;

    case 'import-cauldron':
        $importedCauldronWitchId = $this->wc->request->param('imported-cauldron-witch', null, FILTER_VALIDATE_INT);
        if( !$importedCauldronWitchId )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, import cauldron witch isn't identified",
            ]);
            break;
        }

        $importedCauldronWitch = WitchHandler::createFromId( $this->wc, $importedCauldronWitchId );
        if( !$importedCauldronWitch )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, import cauldron witch couldn't be loaded",
            ]);
            break;
        }
        elseif( !$importedCauldronWitch->hasCauldron() || !$importedCauldronWitch->cauldronId )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, can't find import cauldron",
            ]);
            break;
        }
        elseif( !$this->witch("target")->edit([ 'cauldron' => $importedCauldronWitch->cauldronId ]) )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error occurred, cauldron import failed",
            ]);
            break;
        }

        $this->wc->user->addAlert([
            'level'     =>  'success',
            'message'   =>  "Cauldron was imported",
        ]);
    break;
}

$structuresList = [];
$craftWitches   = [];
if( !$this->witch("target")->hasCraft() ){
    $structuresList = Structure::listStructures( $this->wc );
}
else 
{
    $craftWitchBuffer = [];
    foreach( $this->witch("target")->craft()->getWitches() as $key => $craftWitch )
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
        
        $craftWitchBuffer[ $key ]               = $craftWitch;
        $craftWitchBuffer[ $key ]->breadcrumb   = array_reverse($breadcrumb);
    }
    
    $craftWitches    = [];
    $craftWitches[]  = $craftWitchBuffer[ $this->witch("target")->id ];
    foreach( $craftWitchBuffer as $key => $craftWitch ){
        if( $key !=  $this->witch("target")->id ){
            $craftWitches[] = $craftWitch;
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

$this->view();