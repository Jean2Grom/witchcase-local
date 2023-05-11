<?php
$possibleActionsList = [
    'save',
    'save-and-return',
    'publish',
    'delete',
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

// TODO multi draft management
$draft = $target->getDraft();

if( empty($draft) ){
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Impossible de lire le brouillon"
    ];
}

switch( $action )
{
    case 'publish':
        $publish = true;
    case 'save-and-return':
        $return = true;
    case 'save':
        $publish    = $publish ?? false;
        $return     = $return ?? false;
        
        $params = [];
        foreach( $draft->getEditParams() as $key )
        {
            $value = $this->wc->request->param($key);
            if( isset($value) ){
                $params[ $key ] = $value;
            }
        }
        
        $saved = $draft->update( $params );
        
        if( $saved === false )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, modification annulée"
            ];
            
            $return = false;
        }
        elseif( $saved === 0 && !$publish ){
            $alerts[] = [
                'level'     =>  'warning',
                'message'   =>  "Aucune modification"
            ];
        }
        elseif( $publish )
        {
            if( $draft->publish() === false )
            {
                $alerts[] = [
                    'level'     =>  'error',
                    'message'   =>  "Une erreur est survenue, publication annulée"
                ];
                
                $return = false;
            }
            else {
                $alerts[] = [
                    'level'     =>  'success',
                    'message'   =>  "Publié"
                ];                
            }
        }
        else {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Modifié"
            ];
        }
        
        if( $return )
        {
            $this->wc->user->addAlerts($alerts);

            header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id) );
            exit();
        }
    break;
    
    case 'delete':
        if( !$draft->remove() ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, suppression annulée",
            ];
        }
        else 
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Brouillon supprimé"
            ];
            
            $this->wc->user->addAlerts($alerts);
            
            header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id) );
            exit();
        }
    break;    
}

$cancelHref = false;
if( $targetWitch->invoke == 'root' ){
    $cancelHref = $targetWitch->uri;
}
else {
    $cancelHref = $this->wc->website->getUrl("view?id=".$targetWitch->id);
}

$this->setContext('standard');

include $this->getDesignFile();