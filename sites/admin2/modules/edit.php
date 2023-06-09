<?php
$possibleActionsList = [
    'save-witch-info',
    'save-witch-invoke',
    'create-new-witch',
];

$action = $this->wc->request->param('action');
if( !in_array($action, $possibleActionsList) ){
    $action = false;
}

$alerts         = [];
$targetWitch    = $this->wc->witch("target");
if( !$targetWitch )
{
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Undefined Target Witch"
    ];
    
    $this->wc->user->addAlerts($alerts);
    header( 'Location: '.$this->wc->website->getFullUrl() );
    exit();
}

switch( $action )
{
    case 'save-witch-info':        
        $witchNewData   = [
            'name'      =>  trim($this->wc->request->param('witch-name') ?? ""),
            'data'      =>  trim($this->wc->request->param('witch-data') ?? ""),
            'priority'  =>  $this->wc->request->param('witch-priority', 'POST', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ?? 0,
        ];
        
        if( $witchNewData['name'] === "" ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Witch name is missing"
            ];
        }
        else if( !$targetWitch->edit( $witchNewData ) ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, witch was not updated"
            ];
        }
        else{
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Witch updated"
            ];
            
        }
        
        $this->wc->user->addAlerts($alerts);
    break;
    
    case 'create-new-witch':
        $newWitchData   = [
            'name'      =>  trim($this->wc->request->param('new-witch-name') ?? ""),
            'data'      =>  trim($this->wc->request->param('new-witch-data') ?? ""),
            'priority'  =>  $this->wc->request->param('new-witch-priority', 'POST', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ?? 0,
        ];
        
        if( $newWitchData['name'] === "" )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Witch name is missing"
            ];
            $this->wc->user->addAlerts($alerts);
            break;
        }
        
        $newWitchId = $targetWitch->createDaughter( $newWitchData );
        
        if( !$newWitchId )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, new witch wasn't created"
            ];
            $this->wc->user->addAlerts($alerts);
            break;
        }
        else
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "New witch created"
            ];
            
            $this->wc->user->addAlerts($alerts);
            
            header('Location: '.$this->wc->website->getFullUrl('view?id='.$newWitchId)  );
            exit();
        }
    break;    
    
    case 'save-witch-invoke':
        $site       = trim($this->wc->request->param('witch-site') ?? "");
        
        if( empty($site) ){
            $witchNewData   = [
                'site'      => null,
                'url'       => null,
                'invoke'    => null,
                'status'    => 0,
                'context'   => null,
            ];
        }
        else 
        {
            $witchNewData   = [
                'site'      => $site,
                'url'       => null,
                'invoke'    => null,
                'status'    => 0,
                'context'   => null,
            ];
            
            $invokeArray = $this->wc->request->param('witch-invoke', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            if( $invokeArray && !empty($invokeArray[ $site ]) ){
                $witchNewData['invoke'] = $invokeArray[ $site ];
            }
            
            $statusArray = $this->wc->request->param('witch-status', 'post', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
            if( $statusArray && !empty($statusArray[ $site ]) ){
                $witchNewData['status'] = $statusArray[ $site ];
            }
            
            $contextArray = $this->wc->request->param('witch-context', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            if( $contextArray && !empty($contextArray[ $site ]) ){
                $witchNewData['context'] = $contextArray[ $site ];
            }
            
            $autoUrl        = $this->wc->request->param('witch-automatic-url', 'POST', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            $customFullUrl  = $this->wc->request->param('witch-full-url', 'POST', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            $customUrl      = $this->wc->request->param('witch-url');
            
            if( !$autoUrl && $customFullUrl ){
                $witchNewData['url'] = $customUrl;
            }
            elseif( !$autoUrl )
            {
                $url    =   $targetWitch->findPreviousUrlForSite( $site );
                
                if( substr($url, -1) != '/' && substr($customUrl, 0, 1) != '/'  ){
                    $url    .=  '/';
                }
                
                $url        .=  $customUrl;
                
                $witchNewData['url'] = $url;
            }
        }
        
        if( !empty($site) && empty($witchNewData['invoke']) ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Module to invoke is missing"
            ];
        }
        elseif( !$targetWitch->edit( $witchNewData ) ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, witch was not updated"
            ];
        }
        else {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Witch updated"
            ];            
        }
        
        $this->wc->user->addAlerts($alerts);
        
        //header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id."#tab-invoke-part") );
        //exit();
    break;
}

header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id) );
exit();
