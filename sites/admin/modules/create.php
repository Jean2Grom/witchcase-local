<?php 
/** 
 * @var WC\Module $this 
 */


use WC\Handler\CauldronHandler;
use WC\Structure;
use WC\Tools;
use WC\Website;


$action = Tools::filterAction(
    $this->wc->request->param('action'),
    [
        'create-new-witch',
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

$this->wc->debug( $this->wc->request->inputs() );

switch( $action )
{
    case 'create-new-witch':
        $newWitchData   = [
            'name'      =>  trim($this->wc->request->param('new-witch-name') ?? ""),
            'data'      =>  trim($this->wc->request->param('new-witch-data') ?? ""),
            'priority'  =>  $this->wc->request->param('new-witch-priority', 'POST', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ?? 0,
        ];
        
        if( $newWitchData['name'] === "" )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Witch name is missing"
            ];
            break;
        }
        
        $newWitchId = $this->witch("target")->createDaughter( $newWitchData );
        
        if( !$newWitchId )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, new witch wasn't created"
            ];
            break;
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "New witch created"
        ];
        
        $this->wc->user->addAlerts($alerts);
        header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $newWitchId ]) );
        exit();
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

$status = [ "global" => $this->wc->configuration->read("global", "status") ];
foreach( $websitesList as $site => $website ){
    $status[ $site ] = $website->status;
}

$modules = [];
foreach( $websitesList as $site => $website ){
    $modules[ $site ] = $website->listModules();
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

$cancelHref = $this->wc->website->getUrl("view?id=".$this->witch("target")->id);

$targetWitch    = $this->witch("target");

$this->view();