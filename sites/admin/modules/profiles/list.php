<?php
use WC\User\Profile;
use WC\Website;

$possibleActionsList = [
    'delete-profile',
];

$action = false;
if( filter_has_var(INPUT_POST, "action") ){
    foreach( $possibleActionsList as $possibleAction ){
        if(filter_input(INPUT_POST, "action") == $possibleAction ){
            $action = $possibleAction;
        }
    }
}

$profiles = Profile::listProfiles( $this->wc );

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
ksort($websitesList);

$allSitesModulesListBuffer = [];
foreach( $websitesList as $website ){
    $allSitesModulesListBuffer = array_merge( $allSitesModulesListBuffer, $website->listModules() );
}

$allSitesModulesList = array_unique($allSitesModulesListBuffer);
asort( $allSitesModulesList );

$statusGlobal = $this->wc->configuration->read("global", "status");

//$this->wc->dump( $statusGlobal );
//$this->wc->debug( $websitesList );
$this->wc->dump( $profiles );

$alerts = $this->wc->user->getAlerts();
switch( $action )
{
    case 'delete-profile':
        $profileId = filter_input(INPUT_POST, "profile-id", FILTER_VALIDATE_INT);
        if( empty($profileId) || empty($profiles[ $profileId ]) ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, le profil a supprimer n'a pas été identifié."
            ];
        }
        elseif( !$profiles[ $profileId ]->delete() ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, le profil n'a pas été supprimé."
            ];
        }
        else 
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Le profile \"".$profiles[ $profileId ]->name."\" a bien été supprimé."
            ];
            
            unset($profiles[ $profileId ]);
        }
        
    break;
}

$createProfileHref  = $this->wc->website->getUrl("/profiles/create");
$editProfileHref    = $this->wc->website->getUrl("/profiles/edit?profile=");

$this->wc->debug( $createProfileHref );
$this->wc->debug( $editProfileHref );

$this->view();
