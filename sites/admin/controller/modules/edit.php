<?php

use WC\Website;

$possibleActionsList = [
    'save-witch',
    'save-witch-and-return',
];

$action = false;
if( filter_has_var(INPUT_POST, "action") ){
    foreach( $possibleActionsList as $possibleAction ){
        if(filter_input(INPUT_POST, "action") == $possibleAction ){
            $action = $possibleAction;
        }
    }
}

$sites  = [];
if( $this->wc->website->sitesRestrictions ){
    $sites = $this->wc->website->sitesRestrictions;
}
else {
    $sites = array_keys($this->wc->configuration->sites);
}

$alerts         = $this->wc->user->getAlerts();
$targetWitch    = $this->wc->website->witches["target"] ?? false;
if( !$targetWitch )
{
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Vous ne pouvez pas éditer d'élément inexistant."
    ];
    
    $this->wc->user->addAlerts($alerts);
    header( 'Location: '.$this->wc->request->protocole.'://'.$this->wc->website->currentAccess );
    exit();
}

switch( $action )
{
    case 'save-witch-and-return':
        $return = true;
    case 'save-witch':
        $return = $return ?? false;
        
        $name   = filter_input( INPUT_POST, 'witch-name', FILTER_SANITIZE_STRING );
        
        if( empty($name) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Vous ne pouvez pas créér d'élément sans nom."
            ];
            break;
        }
        
        $site           = trim( filter_input(INPUT_POST,    'witch-site', FILTER_SANITIZE_STRING) );
        if( !empty($site) && !in_array($site, $sites) )
        {
            $site       = "";
            $alerts[]   = [
                'level'     =>  'warning',
                'message'   =>  "Le site envoyé n'est pas dans la liste des sites possibles."
            ];
        }
        
        $data           = trim( filter_input(INPUT_POST,    'witch-data', FILTER_SANITIZE_STRING) );
        $priority       = filter_input( INPUT_POST,         'witch-priority', FILTER_VALIDATE_INT );
        $invoke         = trim( filter_input(INPUT_POST,    'witch-invoke', FILTER_SANITIZE_STRING) );
        $context        = trim( filter_input(INPUT_POST,    'witch-context', FILTER_SANITIZE_STRING) );
        $status         = filter_input( INPUT_POST,         'witch-status', FILTER_VALIDATE_INT );
        
        $autoUrl        = filter_has_var(INPUT_POST,        'witch-automatic-url');
        $customUrl      = trim( filter_input(INPUT_POST,    'witch-custom-url', FILTER_SANITIZE_STRING) );
        $customRootUrl  = filter_has_var(INPUT_POST,        'witch-custom-url-from-root');
        
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
                
                header( 'Location: '.$this->wc->request->protocole.'://'.$this->wc->website->currentAccess.'/view?id='.$targetWitch->id );
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
