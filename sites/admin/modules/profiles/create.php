<?php
use WC\Website;
use WC\User\Profile;

$possibleActionsList = [
    'create-new-profile',
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
    case 'create-new-profile':
        $postData = filter_input_array( 
            INPUT_POST, 
            [
                'new-profile-name'      => FILTER_SANITIZE_STRING,
                'new-profile-site'      => FILTER_SANITIZE_STRING,
                'new-profile-module'    => [
                    'filter'    => FILTER_SANITIZE_STRING,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
                'new-profile-status'    => [
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
                'new-profile-witch-id'    => [
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
                'new-profile-witch-children'    => [
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
                'new-profile-witch-included'    => [
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
                'new-profile-witch-parents'    => [
                    'filter'    => FILTER_VALIDATE_INT,
                    'flags'     => FILTER_REQUIRE_ARRAY, 
                ],
            ]
        );
        
        $name = $postData['new-profile-name'];
        
        if( empty($name) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Vous ne pouvez pas créér de profil sans nom."
            ];
            break;
        }
        
        $site           = trim( $postData['new-profile-site'] );
        if( $site != '*' && !in_array($site, $sites) )
        {
            $site       = "";
            $alerts[]   = [
                'level'     =>  'warning',
                'message'   =>  "Le site envoyé n'est pas dans la liste des sites possibles."
            ];
        }
        
        $policies = [];
        foreach( $postData['new-profile-module'] as $i => $policeModule ){
            if( $i > 0 )
            {
                $policeStatus = $postData['new-profile-status'][ $i ];
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
                
                $witchId = ( !empty($postData['new-profile-witch-id'][ $i ]) )? $postData['new-profile-witch-id'][ $i ]: null;
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
            if( !empty($postData['new-profile-witch-'.$checkboxItem]) ){
                foreach( $postData['new-profile-witch-'.$checkboxItem] as $i ){
                    if( !empty($i) ){
                        $policies[ $i ][ $checkboxItem ] = true;
                    }
                }
            }
        }
        
        $newProfileData = [
            'name'      => $name,
            'site'      => $site,
            'policies'  => $policies,
        ];
        
        $newProfileId = Profile::insert( $this->wc, $newProfileData );
        
        if( !$newProfileId ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, le profil n'a pas été créé."
            ];
        }
        else
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Le profile \"".$name."\" a bien été créé."
            ];
            
            $this->wc->user->addAlerts($alerts);
            
            header('Location: '.$this->wc->website->getFullUrl('profiles') );
            exit();
        }
    break;
}


$this->setContext('standard');

include $this->getDesignFile();