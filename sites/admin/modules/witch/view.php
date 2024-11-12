<?php 
/**  @var WC\Module $this */
namespace WC;

use WC\Handler\CauldronHandler;
use WC\Handler\WitchHandler;
//use WC\Cauldron;
//use WC\Structure;
//use WC\Tools;
//use WC\Website;


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
    //$structures     = $this->wc->configuration->recipes();
    //$ingredients    = WC\Ingredient::list();
    
    //$this->wc->debug( $this->witch("target")->cauldron() );
    $this->wc->debug( $this->wc->configuration->recipes() );
}
*/

$this->wc->debug( $this->wc->request->inputs() );

switch( Tools::filterAction( 
    $this->wc->request->param('action'),
    [
        'remove-cauldron',
        'create-cauldron',
        'import-cauldron',
        'cauldron-add-witch',
        'cauldron-add-new-witch',
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
        if( !in_array($structure, array_keys( $this->wc->configuration->recipes() )) )
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
            'recipe'    =>  $structure,
            'status'    =>  Cauldron::STATUS_DRAFT,
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
    
    case 'cauldron-add-witch':
        $urlHash = "#tab-cauldron-part";
        
        if( !$this->witch("target")->cauldron() )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, no cauldron identified"
            ]);
            break;
        }
        
        $id = $this->wc->request->param('cauldron-new-witch-id', 'post', FILTER_VALIDATE_INT);
        if( !$id )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, no witch identified"
            ]);
            break;
        }
        
        $witch = $this->wc->cairn->searchById($id) ?? WitchHandler::createFromId($this->wc, $id);
        if( !$witch )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, chosen witch unidentified"
            ]);
            break;
        }

        if( !$witch->edit([ 'cauldron' => $this->witch("target")->cauldronId ]) )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, something went wrong"
            ]);
            break;
        }

        $this->wc->user->addAlert([
            'level'     =>  'success',
            'message'   =>  "Cauldron added to witch"
        ]);

        header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $witch->id ]).$urlHash );
        exit();
    break;
    
    case 'cauldron-add-new-witch':
        $urlHash = "#tab-cauldron-part";
        
        if( !$this->witch("target")->cauldron() )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, no cauldron identified"
            ]);
            break;
        }
        
        $id = $this->wc->request->param('cauldron-new-witch-id', 'post', FILTER_VALIDATE_INT);
        if( !$id )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, no witch identified"
            ]);
            break;
        }
        
        $witch = $this->wc->cairn->searchById($id) ?? WitchHandler::createFromId($this->wc, $id);
        if( !$witch )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, chosen witch unidentified"
            ]);
            break;
        }
        
        $newWitchData   = [
            'name'          =>  $this->witch("target")->name,
            'data'          =>  $this->witch("target")->data,
            'cauldron'      =>  $this->witch("target")->cauldronId 
        ];
        
        $newWitch = $witch->createDaughter( $newWitchData );
        
        if( !$newWitch )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, new witch wasn't created"
            ]);
            break;
        }
        
        $this->wc->user->addAlert([
            'level'     =>  'success',
            'message'   =>  "New cauldron's witch created"
        ]);
        
        header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $newWitch->id ]).$urlHash );
        exit();
    break;
    

}

// OLD SCHOOL CRAFT PART
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
// END OLD SCHOOL CRAFT PART

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