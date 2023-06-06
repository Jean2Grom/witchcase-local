<?php
use WC\Website;

$possibleActionsList = [
    'save-witch-info',
    'save-witch-invoke',
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
        'message'   =>  "Can't edit undefined witch"
    ];
    
    $this->wc->user->addAlerts($alerts);
    header( 'Location: '.$this->wc->website->getFullUrl() );
    exit();
}

//$this->wc->dump($_POST);
//$autoUrl        = $this->wc->request->param('witch-automatic-url', 'POST', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
//$customUrl      = $this->wc->request->param('witch-url');
//$customRootUrl  = $this->wc->request->param('witch-full-url', 'POST', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
//$this->wc->dump($autoUrl);
//$this->wc->dump($customRootUrl);
//$this->wc->dump($customUrl);
//$this->wc->dump( $this->wc->request->param('witch-invoke', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) );
//$this->wc->dump( $this->wc->request->param('witch-url', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) );
//    
//$this->wc->debug->die("xxx");//#tab-invoke-part

switch( $action )
{
    case 'save-witch-info':        
        $witchNewData   = [
            'name'      =>  trim($this->wc->request->param('witch-name') ?? ""),
            'data'      =>  trim($this->wc->request->param('witch-data') ?? ""),
            'priority'  =>  $this->wc->request->param('witch-priority', 'POST', FILTER_VALIDATE_INT) ?? 0,
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

        header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id) );
        exit();
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

        header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id) );
        //header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id."#tab-invoke-part") );
        exit();
    break;
    
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
        $invoke         = trim( $this->wc->request->param('witch-invoke')  );
        $context        = trim( $this->wc->request->param('witch-context') );
        $status         = $this->wc->request->param('witch-status', 'POST', FILTER_VALIDATE_INT );
$this->wc->dump($status);        
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

$statusGlobal   = $this->wc->configuration->read("global", "status");
$cancelHref     = $this->wc->website->getUrl("view?id=".$targetWitch->id);

$this->view();
