<?php /** @var WC\Module $this */

$possibleActionsList = [
    'edit-data',
    'edit-priorities',
];

$action = $this->wc->request->param('action');
if( !in_array($action, $possibleActionsList) ){
    $action = false;
}

$alerts = $this->wc->user->getAlerts();
switch( $action )
{
    case 'edit-data':
        $data = $this->wc->request->param('data');
        if( $data == $this->witch->data ){
            $alerts[] = [
                'level'     =>  'warning',
                'message'   =>  "Description identique"
            ];
        }
        elseif( $this->witch->edit([ 'data' => $data ]) ){
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Description mise à jour"
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
        $priorities =  $this->wc->request->param('priorities', 'post',FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        
        $errors     = [];
        $success    = [];
        $daughters  = $this->getDaughters();
        foreach( $priorities as $witchId => $witchPriority )
        {
            $editResult = $daughters[ $witchId ]->edit([ 'priority' => $witchPriority ]);
            
            if( $editResult === false ){
                $errors[] = "La priorité de <strong>".$daughters[ $witchId ]->name."</strong> n'a pas été mise à jour.";
            }
            elseif( $editResult > 0 ) {
                $success[] = "La priorité de <strong>".$daughters[ $witchId ]->name."</strong> a été mise à jour.";
            }
        }
                
        if( empty($errors) && empty($success) ){
            $alerts[] = [
                'level'     =>  'warning',
                'message'   =>  "Aucune modification des priorités"
            ];
        }
        elseif( !empty($errors) && !empty($success) )
        {
            $alerts[] = [
                'level'     =>  'warning',
                'message'   =>  "Des erreurs sont survenues"
            ];
            $alerts[] = [
                'level'     =>  'error',
                'message'   => implode('<br/>', $errors),
            ];
            $alerts[] = [
                'level'     =>  'notice',
                'message'   => implode('<br/>', $success),
            ];
        }
        elseif( !empty($errors) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Une erreur est survenue, les priorités n'ont pas été mise à jour."
            ];
            $alerts[] = [
                'level'     =>  'notice',
                'message'   => implode('<br/>', $errors),
            ];
        }
        elseif( !empty($success) )
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Les priorités ont été mises à jour."
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

$createElementHref = $this->wc->website->getUrl("create?mother=".$this->witch->id);

$this->view();
