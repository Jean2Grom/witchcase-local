<?php /** @var WC\Module $this */

use WC\Handler\WitchHandler;
use WC\Structure;
use WC\Craft\Draft;

$possibleActionsList = [
    'copy-witch',
    'move-witch',
    'edit-priorities',
    'create-new-witch',
    'delete-witch',

    'save-witch-menu-info',
    'save-witch-info',
    
    'create-craft',
    'import-craft',
    'remove-craft',
    'archive-craft',
    'add-craft-witch',
    'switch-craft-main-witch',
    
];

$action = $this->wc->request->param('action');
if( !in_array($action, $possibleActionsList) ){
    $action = false;
}
    
$alerts         = [];
$targetWitch    = $this->witch("target");
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

$urlHash = "";
switch( $action )
{
    case 'copy-witch':
        $originWitchId      = $this->wc->request->param('origin-witch', 'post', FILTER_VALIDATE_INT);
        $destinationWitchId = $this->wc->request->param('destination-witch', 'post', FILTER_VALIDATE_INT);
        if( !$originWitchId || !$destinationWitchId )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, data missing"
            ];
            break;
        }
        
        $originWitch        = WitchHandler::createFromId( $this->wc, $originWitchId);
        $destinationWitch   = WitchHandler::createFromId( $this->wc, $destinationWitchId);        
        if( !$originWitch || !$destinationWitch )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, witch unidentified"
            ];
            break;
        }
        
        if( !$originWitch->copyTo($destinationWitch) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, copy canceled"
            ];
            break;
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "Witch was copied"
        ];
        
        $this->wc->user->addAlerts($alerts);        
        header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $destinationWitch->id ]) );
        exit();    
    break;    
    
    case 'move-witch':
        $originWitchId      = $this->wc->request->param('origin-witch', 'post', FILTER_VALIDATE_INT);
        $destinationWitchId = $this->wc->request->param('destination-witch', 'post', FILTER_VALIDATE_INT);
        if( !$originWitchId || !$destinationWitchId )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, data missing"
            ];            
            break;
        }
        
        $originWitch        = WitchHandler::createFromId( $this->wc, $originWitchId);
        $destinationWitch   = WitchHandler::createFromId( $this->wc, $destinationWitchId);        
        if( !$originWitch || !$destinationWitch )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, witch unidentified"
            ];
            break;
        }
        
        if( !$originWitch->moveTo($destinationWitch) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, move canceled"
            ];
            break;
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "Witch was moved"
        ];
        
        $this->wc->user->addAlerts($alerts);
        header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $destinationWitch->id ]) );
        exit();    
    break;
    
    case 'edit-priorities':
        $priorities = $this->wc->request->param('priorities', 'post', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY) ?? [];
        
        $errors     = [];
        $success    = [];
        foreach( $priorities as $witchId => $witchPriority ){
            if( !$targetWitch->daughters( $witchId )->edit([ 'priority' => $witchPriority ]) ){
                $errors[] = "<strong>".$targetWitch->daughters( $witchId )->name."</strong> priority not updated";
            }
            else {
                $success[] = "<strong>".$targetWitch->daughters( $witchId )->name."</strong> priority updated";
            }
        }
        
        if( empty($errors) ){
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Priorities updated"
            ];
        }
        elseif( empty($success) ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, priorities hasn't been updated"
            ];
        }
        else 
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   => implode('<br/>', $success),
            ];
            
            $alerts[] = [
                'level'     =>  'notice',
                'message'   => implode('<br/>', $errors),
            ];            
        }
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
            break;
        }
        
        $newWitchId = $targetWitch->createDaughter( $newWitchData );
        
        if( !$newWitchId )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, new witch wasn't created"
            ];
            break;
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "New witch created"
        ];
        
        $this->wc->user->addAlerts($alerts);
        header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $newWitchId ]) );
        exit();
    break;    
    
    case 'delete-witch':
        if( $targetWitch->mother() && $targetWitch->delete() )
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Witch removed"
            ];

            $this->wc->user->addAlerts( $alerts );
            header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $targetWitch->mother()->id ]) );
            exit();
        }
        
        $alerts[] = [
            'level'     =>  'error',
            'message'   =>  "Error, witch hasn't been removed",
        ];
    break;
    
    case 'save-witch-menu-info':        
        $witchNewData   = [
            'name'      =>  trim($this->wc->request->param('witch-name') ?? ""),
            'data'      =>  trim($this->wc->request->param('witch-data') ?? ""),
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
    break;
        
    case 'save-witch-info':
        $witchNewData   = [
            'site'      => null,
            'status'    => 0,
            'invoke'    => null,
            'url'       => null,
            'context'   => null,
        ];

        $site       = trim($this->wc->request->param('witch-site') ?? "");        
        if( !empty($site) ){
            $witchNewData['site'] = $site;
        }
        
        $statusArray = $this->wc->request->param('witch-status', 'post', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        if( $statusArray )
        {
            if( empty($site) && !empty($statusArray[ 'no-site-selected' ]) ){
                $witchNewData['status'] = $statusArray[ 'no-site-selected' ];
            }
            elseif( !empty($statusArray[ $site ]) ){
                $witchNewData['status'] = $statusArray[ $site ];                
            }
            
        }
        
        if( !empty($site) )
        {
            $invokeArray = $this->wc->request->param('witch-invoke', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            if( $invokeArray && !empty($invokeArray[ $site ]) ){
                $witchNewData['invoke'] = $invokeArray[ $site ];
            }
            
            if( !empty($witchNewData['invoke']) )
            {
                $contextArray = $this->wc->request->param('witch-context', 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                if( $contextArray && !empty($contextArray[ $site ]) ){
                    $witchNewData['context'] = $contextArray[ $site ];
                }

                $autoUrl        = $this->wc->request->param('witch-automatic-url', 'POST', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
                $customFullUrl  = $this->wc->request->param('witch-full-url', 'POST', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
                $customUrl      = $this->wc->request->param('witch-url');

                if( !$autoUrl )
                {
                    $url    =   "";
                    if( !$customFullUrl )
                    {
                        if( $targetWitch->mother() ){
                            $url .= $targetWitch->mother()->getClosestUrl( $site );
                        }

                        if( substr($url, -1) != '/' 
                                && substr($customUrl, 0, 1) != '/'  
                        ){
                            $url .= '/';
                        }
                    }

                    $url    .=  $customUrl;

                    $witchNewData['url'] = $url;
                }
            }
        }
        
        $edit = $targetWitch->edit( $witchNewData );
        
        if( $edit === 0 ){
            $alerts[] = [
                'level'     =>  'warning',
                'message'   =>  "No update",
            ];
        }
        elseif( !$edit ){
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
    break;
    
    case 'create-craft':
        $urlHash = "#tab-craft-part";
        
        $structure          = $this->wc->request->param('witch-content-structure');
        $isValidStructure   = false;
        
        if( !empty($structure) ){
            foreach( Structure::listStructures( $this->wc ) as $structuresData ){
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
                'message'   =>  "Error, addition cancelled"
            ];
            break;
        }
        
        header( 'Location: '.$this->wc->website->getFullUrl('edit-content', [ 'id' => $targetWitch->id ]) );
        exit();
    break;
    
    case 'import-craft':
        $urlHash = "#tab-craft-part";
        
        if( $targetWitch->hasCraft() )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Witch already has craft"
            ];
            break;
        }
        
        $id = $this->wc->request->param('imported-craft-witch', 'post', FILTER_VALIDATE_INT);
        if( !$id )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, no craft identified"
            ];
            break;
        }
        
        $importCraftWitch = WitchHandler::createFromId( $this->wc, $id);
        if( !$importCraftWitch || !$importCraftWitch->hasCraft() )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, no craft identified"
            ];
            break;
        }
        
        $targetWitch->edit([ 
            'craft_table'   =>  $importCraftWitch->craft_table,
            'craft_fk'      =>  $importCraftWitch->craft_fk,
            'is_main'       =>  0,
        ]);
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "Craft imported"
        ];
    break;


    case 'remove-craft':
        $urlHash = "#tab-craft-part";
        
        if( !$targetWitch->hasCraft() )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Craft hasn't been found",
            ];
            break;
        }
        elseif( !$targetWitch->removeCraft() )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, craft hasn't been removed",
            ];
            break;
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "Craft removed"
        ];
    break;
    
    case 'archive-craft':
        $urlHash = "#tab-craft-part";
        
        if( $targetWitch->craft()->archive() === false )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, archiving cancelled"
            ];
            break;
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "Craft archived"
        ];        
    break;
    
    case 'add-craft-witch':
        $urlHash = "#tab-craft-part";
        
        if( !$targetWitch->hasCraft() )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, no craft identified"
            ];
            break;
        }
        
        $id = $this->wc->request->param('new-mother-witch-id', 'post', FILTER_VALIDATE_INT);
        if( !$id )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, no witch identified"
            ];
            break;
        }
        
        $motherWitch = WitchHandler::createFromId( $this->wc, $id);
        if( !$motherWitch )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, no witch identified"
            ];
            break;
        }
        
        $newWitchData   = [
            'name'          =>  $targetWitch->name,
            'data'          =>  $targetWitch->data,
            'priority'      =>  0,
            'craft_table'   =>  $targetWitch->craft_table,
            'craft_fk'      =>  $targetWitch->craft_fk,
            'is_main'       =>  0,
        ];
        
        $newWitchId = $motherWitch->createDaughter( $newWitchData );
        
        if( !$newWitchId )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, new witch wasn't created"
            ];
            break;
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "New craft position's witch created"
        ];
        
        $this->wc->user->addAlerts($alerts);
        header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $newWitchId ]).$urlHash );
        exit();
    break;
    
    case 'switch-craft-main-witch':
        $urlHash = "#tab-craft-part";
        
        if( !$targetWitch->hasCraft() )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, no craft identified"
            ];
            break;
        }
        
        $craftWitches = $targetWitch->craft()->getWitches();
        
        $id = $this->wc->request->param('main', 'post', FILTER_VALIDATE_INT);
        if( !$id || !in_array($id, array_keys($craftWitches)) )
        {
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, no main witch identified"
            ];
            break;
        }
        
        foreach( $craftWitches as $witchId =>  $craftWitch ){
            $craftWitch->edit([ 'is_main' =>  ($witchId == $id? 1: 0) ]);
        }
        
        $alerts[] = [
            'level'     =>  'success',
            'message'   =>  "Craft main position updated"
        ];
    break;    
}

$this->wc->user->addAlerts($alerts);
header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $targetWitch->id ]).$urlHash );
exit();
