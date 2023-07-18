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
$targetWitch    = $this->wc->witch("target");
if( !$targetWitch )
{
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Craft not found"
    ];
    
    $this->wc->user->addAlerts($alerts);
    header( 'Location: '.$this->wc->website->getFullUrl() );
    exit();
}

$craft      = $targetWitch->craft() ?? false;
if( !$craft )
{
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Craft not found"
    ];
    
    $this->wc->user->addAlerts($alerts);
    header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$targetWitch->id) );
    exit();
}

// TODO multi draft management
$draft = $craft->getDraft();

if( empty($draft) ){
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Draft can't be read"
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
        foreach( $draft->getEditParams() as $param )
        {
            $value = $this->wc->request->param($param['name'] ?? $param, 'post', $param['filter'] ??  FILTER_DEFAULT, $param['option'] ??  0 );                
            
            if( isset($value) ){
                $params[ $param['name'] ?? $param ] = $value;
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

$cancelHref = $this->wc->website->getUrl("view?id=".$targetWitch->id);

$this->view();