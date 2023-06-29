<?php
use WC\User\Profile;

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
