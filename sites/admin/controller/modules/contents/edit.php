<?php

$possibleActionsList = [
    'save-content',
    'save-content-and-return',
    'publish',
];

$action = $this->wc->request->param('action');
if( !in_array($action, $possibleActionsList) ){
    $action = false;
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
    header( 'Location: '.$this->wc->website->getFullUrl() );
    exit();
}

$target      = $targetWitch->target() ?? false;
if( !$target )
{
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Vous ne pouvez pas éditer de contenu inexistant."
    ];
    
    $this->wc->user->addAlerts($alerts);
    header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id) );
    exit();
}

if( empty($target) ){
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Impossible d'identifier le contenu à éditer."
    ];
}

switch( $action )
{
    case 'publish':
        $publish = true;
    case 'save-content-and-return':
        $return = true;
    case 'save-content':
        $publish    = $publish ?? false;
        $return     = $return ?? false;
        
        $params = [];
        foreach( $target->getEditParams() as $key )
        {
            $value = $this->wc->request->param($key);
            if( isset($value) ){
                $params[ $key ] = $value;
            }
        }
        
        $saved = $target->update( $params );
        
        //if( !$save || !$target->save() ){
        if( !$saved && !$publish ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, le contenu n'a pas été modifié."
            ];
        }
        else
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Votre contenu a bien été modifié."
            ];
            
            if( $publish )
            {
                $this->wc->dump($action);
                $this->wc->debug->die('jean');
            }
            
            if( $return )
            {
                $this->wc->user->addAlerts($alerts);
                
                header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id) );
                exit();
            }
        }
    break;
}

$cancelHref = false;
if( $targetWitch->invoke == 'root' ){
    $cancelHref = $targetWitch->uri;
}
else {
    $cancelHref = $this->wc->website->baseUri."/view?id=".$targetWitch->id;
}

$this->setContext('standard');

include $this->getDesignFile();