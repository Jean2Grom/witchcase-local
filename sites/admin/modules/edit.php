<?php

use WC\Website;

$possibleActionsList = [
    'save-witch',
    'save-witch-and-return',
];

$action = $this->wc->request->param('action');
if( !in_array($action, $possibleActionsList) ){
    $action = false;
}

$sites  = [];
if( $this->wc->website->sitesRestrictions ){
    $sites = $this->wc->website->sitesRestrictions;
}
else {
    $sites = array_keys($this->wc->configuration->sites);
}

$alerts         = $this->wc->user->getAlerts();
$targetWitch    = $this->wc->witch("target");
if( !$targetWitch )
{
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Vous ne pouvez pas éditer d'élément inexistant."
    ];
    
    $this->wc->user->addAlerts($alerts);
    header( 'Location: '.$this->wc->website->getFullUrl() );
    exit();
}

switch( $action )
{
    case 'save-witch-and-return':
        $return = true;
    case 'save-witch':
        $return = $return ?? false;
        
        $name   = trim( $this->wc->request->param('witch-name') );
        if( empty($name) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Vous ne pouvez pas créér d'élément sans nom."
            ];
            break;
        }
        
        $site           = trim( $this->wc->request->param('witch-site') );
        if( !empty($site) && !in_array($site, $sites) )
        {
            $site       = "";
            $alerts[]   = [
                'level'     =>  'warning',
                'message'   =>  "Le site envoyé n'est pas dans la liste des sites possibles."
            ];
        }
        
        $data           = trim( $this->wc->request->param('witch-data') );
        $priority       = $this->wc->request->param('witch-priority', 'POST', FILTER_VALIDATE_INT );
        $invoke         = trim( $this->wc->request->param('witch-invoke') );
        $context        = trim( $this->wc->request->param('witch-context') );
        $status         = $this->wc->request->param('witch-status', 'POST', FILTER_VALIDATE_INT );
        
        $autoUrl        = $this->wc->request->param('witch-automatic-url', 'POST', FILTER_VALIDATE_BOOL);
        $customUrl      = trim( $this->wc->request->param('witch-custom-url') );
        $customRootUrl  = $this->wc->request->param('witch-custom-url-from-root', 'POST', FILTER_VALIDATE_BOOL);
        
        $witchNewData   = [
            'name'      =>  $name,
            'site'      =>  $site,
            'data'      =>  $data,
            'priority'  =>  $priority,
            'invoke'    =>  $invoke,
            'context'   =>  $context,
            'status'    =>  $status,
        ];
        
        if( !empty($site) && !$autoUrl ){
            if( $customRootUrl ){
                $witchNewData['url'] = $customUrl;
            }
            else 
            {
                $url    =   $targetWitch->findPreviousUrlForSite( $site );
                
                if( substr($url, -1) != '/' && substr($customUrl, 0, 1) != '/'  ){
                    $url    .=  '/';
                }
                
                $url        .=  $customUrl;
                
                $witchNewData['url'] = $url;
            }
        }
        
        if( !$targetWitch->edit( $witchNewData ) ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, votre élément n'a pas été modifié."
            ];
        }
        else
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Votre élément a bien été modifié."
            ];
            
            if( $return )
            {
                $this->wc->user->addAlerts($alerts);
                
                header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id) );
                exit();
            }
        }
    break;
}

$websitesList   = [];
foreach( $sites as $site ){
    if( $site == $this->wc->website->name ){
        $websitesList[ $site ] = $this->wc->website;
    }
    else {
        $websitesList[ $site ] = new Website( $this->wc, $site );
    }
}

$statusGlobal = $this->wc->configuration->read("global", "status");

if( $targetWitch->invoke == 'root' ){
    $cancelHref = $targetWitch->uri;
}
else {
    $cancelHref = $this->wc->website->baseUri."/view?id=".$targetWitch->id;
}

$this->setContext('standard');

include $this->getDesignFile();
