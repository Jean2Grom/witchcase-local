<?php
use WC\Website;
use WC\Profile;

$possibleActionsList = [
    'edit-profile',
    'edit-profile-and-return',
];

$action = false;
if( filter_has_var(INPUT_POST, "action") ){
    foreach( $possibleActionsList as $possibleAction ){
        if(filter_input(INPUT_POST, "action") == $possibleAction ){
            $action = $possibleAction;
        }
    }
}


$targetProfileId    = filter_input(INPUT_GET, 'profile', FILTER_VALIDATE_INT);
$targetProfile      = Profile::listProfiles($this->wc, [ 'profile.id' =>  $targetProfileId ])[ $targetProfileId ];


$sites  = [];
if( $this->wc->website->sitesRestrictions ){
    $sites = $this->wc->website->sitesRestrictions;
}
else {
    $sites = array_keys($this->wc->configuration->sites);
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

$globalModulesList = $this->wc->website->listModules();
foreach( $websitesList as $website ){
    $globalModulesList = array_intersect($globalModulesList, $website->listModules());
}

$statusGlobal = $this->wc->configuration->read("global", "status");

$alerts = $this->wc->user->getAlerts();
switch( $action )
{
    case 'edit-profile-and-return':
        $return = true;
    case 'edit-profile':
        $return = $return ?? false;
    
        $postData = filter_input_array( 
            INPUT_POST, 
            [
                'profile-name'      => FILTER_SANITIZE_STRING,
                'profile-site'      => FILTER_SANITIZE_STRING,
                'profile-module'    => [
                    'filter'    => FILTER_SANITIZE_STRING,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
                'profile-status'    => [
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
                'profile-witch-id'    => [
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
                'profile-witch-children'    => [
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
                'profile-witch-included'    => [
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
                'profile-witch-parents'    => [
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
            ]
        );
        
        $name = $postData['profile-name'];
        
        if( empty($name) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Vous ne pouvez pas créér de profil sans nom."
            ];
            break;
        }
        
        $site           = trim( $postData['profile-site'] );
        if( $site != '*' && !in_array($site, $sites) )
        {
            $site       = "";
            $alerts[]   = [
                'level'     =>  'warning',
                'message'   =>  "Le site envoyé n'est pas dans la liste des sites possibles."
            ];
        }
        
        $policies = [];
        foreach( $postData['profile-module'] as $i => $policeModule ){
            if( $i > 0 )
            {
                $policeStatus = $postData['profile-status'][ $i ];
                if( $site == '*'
                        && ( ($policeModule != '*' && !in_array( $policeModule, $globalModulesList )) 
                            || ($policeStatus != '-1' && !in_array( $policeStatus, array_keys($statusGlobal) )) )
                ){
                    continue;
                }
                elseif( $site != '*'
                            && ( ($policeModule != '*' && !in_array( $policeModule, $websitesList[ $site ]->listModules() )) 
                                || ($policeStatus != '-1' && !in_array( $policeStatus, array_keys($websitesList[ $site ]->status) )) )
                ){
                    continue;
                }
                
                $witchId = ( !empty($postData['profile-witch-id'][ $i ]) )? $postData['profile-witch-id'][ $i ]: null;
                if( $policeStatus < 0 ){
                    $policeStatus = null;
                }
                    
                $policies[ $i ] = [ 
                    'module'    => $policeModule, 
                    'status'    => $policeStatus, 
                    'witchId'   => $witchId,
                    'parents'   => false,
                    'children'  => false,
                    'included'  => false,
                    'custom'    => "",
                ];
            }
        }
        
        foreach( ['parents', 'children', 'included'] as $checkboxItem ){
            if( !empty($postData['profile-witch-'.$checkboxItem]) ){
                foreach( $postData['profile-witch-'.$checkboxItem] as $i ){
                    if( !empty($i) ){
                        $policies[ $i ][ $checkboxItem ] = true;
                    }
                }
            }
        }
        
        $profileData = [
            'name'      => $name,
            'site'      => $site,
            'policies'  => $policies,
        ];
        
        $edition = $targetProfile->edit( $profileData );
        
        $targetProfile      = Profile::listProfiles($this->wc, [ 'profile.id' =>  $targetProfileId ])[ $targetProfileId ];
        
        if( !$edition ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, le profil n'a pas été modifié."
            ];
        }
        else
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Le profile \"".$name."\" a bien été mis à jour."
            ];
            
            if( $return )
            {
                $this->wc->user->addAlerts($alerts);

                header('Location: '.$this->wc->website->getFullUrl('profiles') );
                exit();
            }
            
            
        }
    break;
}


$this->setContext('standard');

include $this->getDesignFile();