<?php
$possibleActionsList = [
    'edit-data',
    'edit-priorities',
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
    case 'edit-data':
        $data = filter_input( INPUT_POST, 'data', FILTER_SANITIZE_STRING );
        if( $data == $this->witch->data ){
            $alerts[] = [
                'level'     =>  'warning',
                'message'   =>  "La description est identique, elle n'a pas été mise à jour."
            ];
        }
        elseif( $this->witch->edit([ 'data' => $data ]) ){
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "La description a été mise à jour."
            ];
        }
        else {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, la description n'a pas été mise à jour."
            ];
        }
    break;
    
    case 'edit-priorities':
        $priorities = filter_input( INPUT_POST, 'priorities', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );
        
        $errors     = [];
        $success    = [];
        foreach( $priorities as $witchId => $witchPriority )
        {
            $daughters = $this->getDaughters();
            if( !$daughters[ $witchId ]->edit([ 'priority' => $witchPriority ]) ){
                $errors[] = "La priorité de <strong>".$daughters[ $witchId ]->name."</strong> n'a pas été mise à jour.";
            }
            else {
                $success[] = "La priorité de <strong>".$daughters[ $witchId ]->name."</strong> a été mise à jour.";
            }
        }
        
        $this->witch->reorderDaughters();
        
        if( empty($errors) ){
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Les priorités ont été mises à jour."
            ];
        }
        elseif( empty($success) ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, les priorités n'ont pas été mise à jour."
            ];
        }
        else 
        {
            $alerts[] = [
                'level'     =>  'warning',
                'message'   => implode('<br/>', $errors),
            ];
            
            $alerts[] = [
                'level'     =>  'notice',
                'message'   => implode('<br/>', $success),
            ];
        }
        
    break;
}

$subTree = [
    'headers' => [
        'Nom', 
        'Site', 
        'Type', 
        'Priorité',
    ],
    'data'  =>  $this->getDaughters(),
];

$createElementHref = $this->wc->website->baseUri."/create?mother=".$this->witch->id;

$this->setContext('standard');

include $this->getDesignFile();
