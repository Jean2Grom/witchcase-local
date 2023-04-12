<?php

use WC\Website;
use WC\TargetStructure;

$possibleActionsList = [
    'create-new-witch',
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
        
        $name = $this->wc->request->param('new-witch-name');
        if( empty($name) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Vous ne pouvez pas créér d'élément sans nom."
            ];
            break;
        }
        
        $site           = trim( $this->wc->request->param('new-witch-site') );
        if( !empty($site) && !in_array($site, $sites) )
        {
            $site       = "";
            $alerts[]   = [
                'level'     =>  'warning',
                'message'   =>  "Le site envoyé n'est pas dans la liste des sites possibles."
            ];
        }
        
        $data           = trim( $this->wc->request->param('new-witch-data') );
        $priority       = $this->wc->request->param('new-witch-priority', 'POST', FILTER_VALIDATE_INT );
        $invoke         = trim( $this->wc->request->param('new-witch-invoke') );
        $context        = trim( $this->wc->request->param('new-witch-context') );
        $status         = $this->wc->request->param('new-witch-status', 'POST', FILTER_VALIDATE_INT );
        
        $autoUrl        = $this->wc->request->param('new-witch-automatic-url', 'POST', FILTER_VALIDATE_BOOL);
        $customUrl      = trim( $this->wc->request->param('new-witch-custom-url') );        
        $customRootUrl  = $this->wc->request->param('new-witch-custom-url-from-root', 'POST', FILTER_VALIDATE_BOOL);       
        
        $structure      = trim( $this->wc->request->param('new-witch-structure') );       
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
