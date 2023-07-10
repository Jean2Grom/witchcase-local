<?php
use WC\User\Profile;
use WC\Website;

$possibleActionsList = [
    'create-profile',
    'edit-profile',
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

$alerts = $this->wc->user->getAlerts();
switch( $action )
{
    case 'create-profile':
        
        $profileData = [
            'name'      =>  trim($this->wc->request->param('profile-name') ?? ""),
        ];
        
        if( empty($profileData['name']) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Name is mandatory, creation canceled"
            ];
            break;
        }
        
        $site                   = trim($this->wc->request->param('profile-site') ?? "");
        $profileData['site']    = $site;
        
        $policyIds  = $this->wc->request->param('policy-id', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? []; 
        $witches    = $this->wc->request->param('policy-witch-id', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        $custom     = $this->wc->request->param('policy-custom', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        
        $modulesRaw = $this->wc->request->param('policy-module', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? []; 
        $modules    = $modulesRaw[ $site ] ?? [];
        
        $statusRaw  = $this->wc->request->param('policy-status', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? []; 
        $status     = $statusRaw[ $site ] ?? [];        
        
        $witchesRulesAncestor       = $this->wc->request->param('policy-witch-rules-ancestors', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        $witchesRulesSelf           = $this->wc->request->param('policy-witch-rules-self', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        $witchesRulesDescendants    = $this->wc->request->param('policy-witch-rules-descendants', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        
        $profileData['policies'] = [];
        foreach( $policyIds as $key => $pId )
        {
            if( $pId == -1 ){
                continue;
            }
            
            $data = [
                'module' => $modules[ $key ],
                'status' => $status[ $key ],
                'status' => $status[ $key ],
                'witch'  => $witches[ $key ],
                'custom' => $custom[ $key ],
            ];
            
            if( !empty($data['witch']) ){
                $data['witchRules'] = [
                    'ancestors'     => in_array($pId, $witchesRulesAncestor)? true: false,
                    'self'          => in_array($pId, $witchesRulesSelf)? true: false,
                    'descendants'   => in_array($pId, $witchesRulesDescendants)? true: false,
                ];
            }
            
            $profileData['policies'][] = $data;
        }        
        
        if( empty($profileData['site']) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Mandatory parameters missing, creation canceled"
            ];
            break;
        }
        
        $newProfileId = Profile::createNew( $this->wc, $profileData );
        if( !$newProfileId )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Creation failed"
            ];
            break;
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "Creation succeed"
        ];
    break;
    
    case 'edit-profile': 
        $profileData = [
            'id'      =>  $this->wc->request->param('profile-id', 'post', FILTER_VALIDATE_INT),
        ];
        
        
        if( empty($profileData['id']) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Unidentified profile, edition canceled"
            ];
            break;
        }
        
        $profileData['name']    = trim($this->wc->request->param('profile-name') ?? "");
        $profileData['site']    = trim($this->wc->request->param('profile-site') ?? "");
        
        $site                   = $profileData['site'] !== '*'? $profileData['site']: 'all';
        
        $policyIds  = $this->wc->request->param('policy-id', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? []; 
        $witches    = $this->wc->request->param('policy-witch-id', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        $custom     = $this->wc->request->param('policy-custom', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        
        $modulesRaw = $this->wc->request->param('policy-module', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? []; 
        $modules    = $modulesRaw[ $site ] ?? [];
        
        $statusRaw  = $this->wc->request->param('policy-status', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? []; 
        $status     = $statusRaw[ $site ] ?? [];        
        
        $witchesRulesAncestor       = $this->wc->request->param('policy-witch-rules-ancestors', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        $witchesRulesSelf           = $this->wc->request->param('policy-witch-rules-self', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        $witchesRulesDescendants    = $this->wc->request->param('policy-witch-rules-descendants', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
        
        $profileData['policiesToDelete'] = array_filter($this->wc->request->param('policy-deleted', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? []);
        
        $profileData['policies'] = [];
        foreach( $policyIds as $key => $pId )
        {
            if( $pId == -1 || in_array($pId, $profileData['policiesToDelete'])){
                continue;
            }
            
            $data = [
                'module' => $modules[ $key ],
                'status' => $status[ $key ],
                'status' => $status[ $key ],
                'witch'  => $witches[ $key ],
                'custom' => $custom[ $key ],
            ];
            
            if( is_numeric($pId) ){
                $data['id'] = $pId;
            }
            
            if( !empty($data['witch']) ){
                $data['witchRules'] = [
                    'ancestors'     => in_array($pId, $witchesRulesAncestor)? true: false,
                    'self'          => in_array($pId, $witchesRulesSelf)? true: false,
                    'descendants'   => in_array($pId, $witchesRulesDescendants)? true: false,
                ];
            }
            
            $profileData['policies'][] = $data;
        }
        
        if( !Profile::edit( $this->wc, $profileData ) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Edition failed"
            ];
            break;
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "Profile updated"
        ];
        
    break;

    case 'delete-profile':
        $profileId = $this->wc->request->param('profile-id', 'post', FILTER_VALIDATE_INT);
        if( empty($profileId) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Unidentified profile, deletion canceled"
            ];
            break;
        }
        
        $profile = Profile::createFromId($this->wc, $profileId);
        if( !$profile )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Unidentified profile, deletion canceled"
            ];
            break;
        }

        if( !$profile->delete() )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Deletion failed"
            ];
            break;
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "Profile ".$profile->name." deleted"
        ];
    break;
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

$this->view();
