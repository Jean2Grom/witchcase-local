<?php 
/** @var WC\Module $this */
namespace WC;

use WC\Handler\CauldronHandler;
use WC\Handler\WitchHandler;
//use WC\Cauldron;
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

$this->wc->dump( $_POST );

switch(Tools::filterAction(
    $this->wc->request->param('action'),
    [
        'create-new-witch',
    ], 
))
{
    case 'create-new-witch':
        $params         = [];
        $params['name'] = trim($this->wc->request->param('new-witch-name') ?? "");
        if( !$params['name'] )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Witch name is missing"
            ]);
            break;
        }

        $data           = trim($this->wc->request->param('new-witch-data') ?? "");
        if( $data ){
            $params['data'] = $data;
        }

        $priority = $this->wc->request->param('new-witch-priority', 'POST', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if( !is_null($priority) ){
            $params['priority'] = $priority;
        }

        $status = $this->wc->request->param('new-witch-status', 'POST', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if( !is_null($status) ){
            $params['status'] = $status;
        }

        $site = trim($this->wc->request->param('new-witch-site') ?? "");
        if( in_array($site, $sites) ){
            $params['site'] = $site;
        }

        $invoke = trim($this->wc->request->param('new-witch-invoke') ?? "");
        if( $params['site'] && in_array($invoke, $modules[ $site ]) ){
            $params['invoke'] = $invoke;
        }

        $autoUrl        = $this->wc->request->param('new-witch-automatic-url', 'POST', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        $customFullUrl  = $this->wc->request->param('new-witch-full-url', 'POST', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        $customUrl      = $this->wc->request->param('new-witch-url');
        
        if( $params['invoke'] && !$autoUrl )
        {
            $url    =   "";
            if( !$customFullUrl )
            {
                $url    .=  trim( $this->witch("target")->getClosestUrl($site), '/' );
                $url    .=  '/';
            }
            $url    .=  trim( $customUrl, '/' );

            $params['url'] = trim( $url, '/' );
        }


        $importCauldronWitchId  = $this->wc->request->param('imported-cauldron-witch');
        $recipe                 = $this->wc->request->param('new-witch-cauldron-recipe');

        // Cauldron importation
        if( $importCauldronWitchId )
        {
            $importCauldronWitch = WitchHandler::fetch( $this->wc, $importCauldronWitchId );

            if( !$importCauldronWitch 
                || !$importCauldronWitch->hasCauldron()
                || !$importCauldronWitch->cauldronId
            ){
                $this->wc->user->addAlert([
                    'level'     =>  'Error',
                    'message'   =>  "Cauldron couldn't be imported",
                ]);
                break;
            }

            $params['cauldron'] = $importCauldronWitch->cauldronId;    
        }
        // Cauldron creation
        elseif( $recipe && in_array($recipe, array_keys( $this->wc->configuration->recipes() )) )
        {
            $folderCauldron = CauldronHandler::getStorageStructure($this->wc, 
                $this->wc->website->site, 
                $recipe
            );
            $newCauldron = CauldronHandler::createFromData($this->wc, [
                'name'      =>  $params['name'],
                'recipe'    =>  $recipe,
                'status'    =>  Cauldron::STATUS_DRAFT,
            ]);

            if( !$folderCauldron 
                || !$newCauldron 
                || !$folderCauldron->addCauldron( $newCauldron ) 
                || !$newCauldron->save() 
            ){
                $this->wc->user->addAlert([
                    'level'     =>  'warning',
                    'message'   =>  "Warning, Cauldron creation has failed",
                ]);
                break;
            }

            $params['cauldron'] = $newCauldron->id;
        }

        $newWitch = $this->witch("target")->createDaughter( $params );

        if( !$newWitch ){
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, new witch wasn't created"
            ]);
            break;
        }
        
        $this->wc->user->addAlert([
            'level'     =>  'success',
            'message'   =>  "New witch created"
        ]);

        if( isset($newCauldron) ){
            $url = "cauldron";
        }
        else {
            $url = "view";
        }

        $this->wc->db->commit();
        header( 'Location: '.$this->wc->website->getFullUrl($url, [ 'id' => $newWitch->id ]) );
        exit();
    break;    
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