<?php
use WC\Structure;
use WC\Craft\Draft;

$possibleActionsList = [
    'edit-priorities',
    'delete-witch',
    'delete-content',
    'add-content',
    'archive-content',
];

$action = $this->wc->request->param('action');
if( !in_array($action, $possibleActionsList) ){
    $action = false;
}

$targetWitch = $this->wc->witch("target");

if( !$targetWitch ){
    $alert = [
        'level'     =>  'error',
        'message'   =>  "L'élément devant être visualisé n'a pas été trouvé."
    ];
    $this->wc->user->addAlerts([ $alert ]);
    
    header('Location: '.$this->wc->website->getRootUrl() );
    exit();
}

$upLink = false;
if( $targetWitch->mother() !== false ){
    $upLink = $this->wc->website->getUrl("view?id=".$targetWitch->mother()->id);
}

$structuresList = [];
if( !$targetWitch->hasCraft() ){
    $structuresList = Structure::listStructures( $this->wc );
}

$alerts = $this->wc->user->getAlerts();
switch( $action )
{
    case 'edit-priorities':
        $priorities = filter_input( INPUT_POST, 'priorities', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );
        
        $errors     = [];
        $success    = [];
        foreach( $priorities as $witchId => $witchPriority ){
            if( !$targetWitch->daughters( $witchId )->edit([ 'priority' => $witchPriority ]) ){
                $errors[] = "La priorité de <strong>".$targetWitch->daughters( $witchId )->name."</strong> n'a pas été mise à jour.";
            }
            else {
                $success[] = "La priorité de <strong>".$targetWitch->daughters( $witchId )->name."</strong> a été mise à jour.";
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
        if( !$targetWitch->hasCraft() ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Le contenu n'a pas été supprimé car il n'a pas été trouvé.",
            ];
        }
        elseif( !$targetWitch->removeCraft() ){
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
            
            $structuresList = Structure::listStructures( $this->wc );
        }
    break;
    
    case 'add-content':
        $structure          = $this->wc->request->param('witch-content-structure');
        $isValidStructure   = false;
        
        if( !empty($structure) ){
            foreach( $structuresList as $structuresData ){
                if( $structuresData['name'] == $structure )
                {
                    $isValidStructure = true;
                    break;
                }
            }
        }
        
        if( !$isValidStructure 
            || !$targetWitch->addStructure(new Structure( $this->wc, $structure, Draft::TYPE )) 
        ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, ajout annulé"
            ];            
        }
        else
        {
            header( 'Location: '.$this->wc->website->getFullUrl('edit-content?id='.$targetWitch->id) );
            exit();
        }
    break;
    
    case 'archive-content':
        if( $targetWitch->craft()->archive() === false ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, archivage annulé"
            ];
        }      
    break;
    
}

$editCraftWitchHref    = $this->wc->website->getUrl("edit?id=".$targetWitch->id);
$createElementHref     = $this->wc->website->getUrl("create?mother=".$targetWitch->id);
$editCraftContentHref  = $this->wc->website->getUrl("edit-content?id=".$targetWitch->id);

$subTree = [
    'headers'   => [
        'Nom', 
        'Site', 
        'Type', 
        'Priorité',
    ],
    'data'      =>  $targetWitch->daughters(),
];

$this->view();