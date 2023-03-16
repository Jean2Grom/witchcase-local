<?php

use WC\Website;
use WC\TargetStructure;

$possibleActionsList = [
    'create-new-witch',
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

$structuresList = TargetStructure::listStructures( $this->wc );

$motherWitch = $this->wc->website->witches["mother"] ?? false;

$alerts = $this->wc->user->getAlerts();
if( !$motherWitch ){
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Vous ne pouvez pas créér d'élément en dehors de l'arborescence."
    ];
}

switch( $action )
{
    case 'create-new-witch':
        if( !$motherWitch )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Vous ne pouvez pas créér d'élément en dehors de l'arborescence."
            ];
            break;
        }
        
        $name = filter_input( INPUT_POST, 'new-witch-name', FILTER_SANITIZE_STRING );
        
        if( empty($name) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Vous ne pouvez pas créér d'élément sans nom."
            ];
            break;
        }
        
        $site           = trim( filter_input(INPUT_POST,    'new-witch-site', FILTER_SANITIZE_STRING) );
        if( !empty($site) && !in_array($site, $sites) )
        {
            $site       = "";
            $alerts[]   = [
                'level'     =>  'warning',
                'message'   =>  "Le site envoyé n'est pas dans la liste des sites possibles."
            ];
        }
        
        $data           = trim( filter_input(INPUT_POST,    'new-witch-data', FILTER_SANITIZE_STRING) );
        $priority       = filter_input( INPUT_POST,         'new-witch-priority', FILTER_VALIDATE_INT );
        $invoke         = trim( filter_input(INPUT_POST,    'new-witch-invoke', FILTER_SANITIZE_STRING) );
        $context        = trim( filter_input(INPUT_POST,    'new-witch-context', FILTER_SANITIZE_STRING) );
        $status         = filter_input( INPUT_POST,         'new-witch-status', FILTER_VALIDATE_INT );
        
        $autoUrl        = filter_has_var(INPUT_POST,        'new-witch-automatic-url');
        $customUrl      = trim( filter_input(INPUT_POST,    'new-witch-custom-url', FILTER_SANITIZE_STRING) );
        $customRootUrl  = filter_has_var(INPUT_POST,        'new-witch-custom-url-from-root');
        
        $structure      = trim( filter_input(INPUT_POST,    'new-witch-structure', FILTER_SANITIZE_STRING) );
        
        if( !empty($structure) )
        {
            $isValidStructure = false;
            foreach( $structuresList as $structuresData ){
                if( $structuresData['table'] == $structure )
                {
                    $isValidStructure = true;
                    break;
                }
            }
        }
        
        $newWitchData   = [
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
                $newWitchData['url'] = $customUrl;
            }
            else 
            {
                if( $motherWitch->site == $site ){
                    $url    =   $motherWitch->url;
                }
                else {
                    $url    =   $motherWitch->findPreviousUrlForSite( $site );
                }
                
                if( substr($url, -1) != '/' && substr($customUrl, 0, 1) != '/'  ){
                    $url    .=  '/';
                }
                
                $url        .=  $customUrl;
                
                $newWitchData['url'] = $url;
            }
        }
        
        if( !empty($structure) && $isValidStructure )
        {
            $targetStructure = new TargetStructure($this->wc, $structure);
            $targetId        = $targetStructure->createTarget( $name );
            
            $newWitchData['target_table']   = $targetStructure->table;
            $newWitchData['target_fk']      = $targetId;
        }
        
        $newWitchId = $motherWitch->createDaughter( $newWitchData );
        
        if( !$newWitchId ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, votre élément n'a pas été créé."
            ];
        }
        elseif( !empty($structure) && !empty($targetId) )
        {
            header('Location: '.$this->wc->request->protocole.'://'.$this->wc->website->currentAccess.'/edit-content?id='.$newWitchId );
            exit();
        }
        else
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Votre élément a bien été créé."
            ];
            
            $this->wc->user->addAlerts($alerts);
            
            header('Location: '.$this->wc->request->protocole.'://'.$this->wc->website->currentAccess.'/view?id='.$newWitchId );
            exit();
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

$cancelHref = false;
if( $motherWitch ){
    if( $motherWitch->invoke == 'root' ){
        $cancelHref = $motherWitch->uri;
    }
    else {
        $cancelHref = $this->wc->website->baseUri."/view?id=".$motherWitch->id;
    }
}

$this->setContext('standard');

include $this->getDesignFile();
