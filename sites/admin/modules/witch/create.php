<?php 
/** 
 * @var WC\Module $this 
 */

use WC\Tools;
use WC\Website;

$action = Tools::filterAction(
    $this->wc->request->param('action'),
    [
        'create-new-witch',
    ], 
);

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

switch( $action )
{
    case 'create-new-witch':
        $newWitchData   = [
            'name'      =>  null,
            'data'      =>  null,
            'priority'  =>  $this->wc->request->param('new-witch-priority', 'POST', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ?? 0,
            'site'      =>  null,
            'status'    =>  $this->wc->request->param('new-witch-status', 'POST', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ?? 0,
            'invoke'    =>  null,
            'url'       =>  null,
            'context'   =>  null,
        ];
        
        $name = trim($this->wc->request->param('new-witch-name') ?? "");
        if( $name === "" )
        {
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Witch name is missing"
            ]);
            break;
        }
        $newWitchData['name'] = $name;

        $data = trim($this->wc->request->param('new-witch-data') ?? "");
        if( $data !== "" ){
            $newWitchData['data'] = $data;
        }

        $site = trim($this->wc->request->param('new-witch-site') ?? "");
        if( in_array($site, $sites) )
        {
            $newWitchData['site'] = $site;

            $invoke = trim($this->wc->request->param('new-witch-invoke') ?? "");
            if( in_array($invoke, $modules[ $site ]) )
            {
                $newWitchData['invoke'] = $invoke;

                $context = trim($this->wc->request->param('new-witch-context') ?? "");
                if( $context !== "" ){
                    $newWitchData['context'] = $context;
                }

                $autoUrl        = $this->wc->request->param('new-witch-automatic-url', 'POST', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
                $customFullUrl  = $this->wc->request->param('new-witch-full-url', 'POST', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
                $customUrl      = $this->wc->request->param('new-witch-url');
                
                if( !$autoUrl )
                {
                    $url    =   "";
                    if( !$customFullUrl )
                    {
                        if( $this->witch("target")->mother() ){
                            $url .= $this->witch("target")->mother()->getClosestUrl( $site );
                        }

                        if( substr($url, -1) != '/' 
                                && substr($customUrl, 0, 1) != '/'  
                        ){
                            $url .= '/';
                        }
                    }

                    $url    .=  $customUrl;

                    $newWitchData['url'] = $url;
                }
            }
        }
        
        $newWitchId = $this->witch("target")->createDaughter( $newWitchData );
        
        if( !$newWitchId ){
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
        
        header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $newWitchId ]) );
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