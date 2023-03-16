<?php

use WC\TargetStructure;

$possibleActionsList = [
    'edit-priorities',
    'delete-witch',
    'delete-content',
    'witch-add-content',
];

$action = false;
if( filter_has_var(INPUT_POST, "action") ){
    foreach( $possibleActionsList as $possibleAction ){
        if(filter_input(INPUT_POST, "action") == $possibleAction ){
            $action = $possibleAction;
        }
    }
}

$targetWitch = $this->wc->website->witches["target"] ?? false;

if( !$targetWitch ){
    $alert = [
        'level'     =>  'error',
        'message'   =>  "L'élément devant être visualisé n'a pas été trouvé."
    ];
    $this->wc->user->addAlerts([ $alert ]);
    
    header('Location: '.$this->wc->website->baseUri );
    exit();
}

$upLink = false;
if( !empty($targetWitch->mother) ){
    if( $targetWitch->mother->invoke == 'root' ){
        $upLink = $targetWitch->mother->uri;
    }
    else {
        $upLink = $this->wc->website->baseUri."/view?id=".$targetWitch->mother->id;
    }
}

$structuresList = [];
if( !$targetWitch->hasTarget() ){
    $structuresList = TargetStructure::listStructures( $this->wc );
}

$alerts = $this->wc->user->getAlerts();
switch( $action )
{
    case 'edit-priorities':
        $priorities = filter_input( INPUT_POST, 'priorities', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );
        
        $errors     = [];
        $success    = [];
        foreach( $priorities as $witchId => $witchPriority ){
            if( !$targetWitch->daughters[ $witchId ]->edit([ 'priority' => $witchPriority ]) ){
                $errors[] = "La priorité de <strong>".$targetWitch->daughters[ $witchId ]->name."</strong> n'a pas été mise à jour.";
            }
            else {
                $success[] = "La priorité de <strong>".$targetWitch->daughters[ $witchId ]->name."</strong> a été mise à jour.";
            }
        }
        
        $targetWitch->reorderDaughters();
        
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
    
    case 'delete-witch':
        if( $upLink && $targetWitch->delete() )
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "L'élément a bien été supprimé."
            ];

            $this->wc->user->addAlerts( $alerts );

            header('Location: '.$upLink );
            exit();
        }
        
        $alerts[] = [
            'level'     =>  'error',
            'message'   =>  "Une erreur est survenue, l'élément n'a pas été supprimé.",
        ];
    break;
    
    case 'delete-content':
        if( !$targetWitch->hasTarget() ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Le contenu n'a pas été supprimé car il n'a pas été trouvé.",
            ];
        }
        elseif( !$targetWitch->deleteContent() ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, le contenu n'a pas été supprimé.",
            ];
        }
        else 
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Le contenu a bien été supprimé."
            ];
            
            $structuresList = TargetStructure::listStructures( $this->wc );
        }
    break;
    
    case 'witch-add-content':
        $structure      = trim( filter_input(INPUT_POST,    'witch-structure', FILTER_SANITIZE_STRING) );
        
        if( !empty($structure) )
        {
            $isValidStructure = false;
            foreach( $structuresList as $structuresData ){
                if( $structuresData['table'] == $structure )
                {
                    $isValidStructure = true;
                    break;
                }
            }
        }
        
        $witchData = [];
        if( !empty($structure) && $isValidStructure )
        {
            $targetStructure = new TargetStructure($this->wc, $structure);
            $targetId        = $targetStructure->createTarget( $targetWitch->name );
            
            $witchData['target_table']   = $targetStructure->table;
            $witchData['target_fk']      = $targetId;
        }
        
        if( empty($structure) || empty($targetId) || !$targetWitch->edit( $witchData ) ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, votre contenu n'a pas été ajouté."
            ];
        }
        else
        {
            header('Location: '.$this->wc->request->protocole.'://'.$this->wc->website->currentAccess.'/edit-content?id='.$targetWitch->id );
            exit();
        }
    break;
}

$editTargetWitchHref    = $this->wc->website->baseUri."/edit?id=".$targetWitch->id;
$createElementHref      = $this->wc->website->baseUri."/create?mother=".$targetWitch->id;
$editTargetContentHref  = $this->wc->website->baseUri."/edit-content?id=".$targetWitch->id;

$subTree = [
    'headers'   => [
        'Nom', 
        'Site', 
        'Type', 
        'Priorité',
    ],
    'data'      =>  $targetWitch->daughters,
];

$this->setContext('standard');

include $this->getDesignFile();
