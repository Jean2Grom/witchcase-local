<?php

$possibleActionsList = [
    'save-content',
    'save-content-and-return',
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
    case 'save-content-and-return':
        $return = true;
    case 'save-content':
        $return = $return ?? false;
        
        $save =true;
        foreach( $target->attributes as $attribute ){
            foreach( $attribute->tableColumns as $attributeElement => $tableColumnName )
            {
                $value =  $this->wc->request->param($tableColumnName);
                
                if( $value && !$attribute->setValue($attributeElement, $value) )
                {
                    $alerts = array_merge($alerts, $this->wc->user->getAlerts());
                    $save   = false;
                    break;
                }
            }
        }
        
        if( !$save || !$target->save() ){
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