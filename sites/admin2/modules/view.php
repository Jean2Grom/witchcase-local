<?php
use WC\Structure;
use WC\Craft\Draft;
use WC\Website;
use WC\Witch;

$possibleActionsList = [
    'edit-priorities',
    'delete-witch',
    'delete-content',
    'add-content',
    'archive-content',
    'add-position',
];

$action = $this->wc->request->param('action');
if( !in_array($action, $possibleActionsList) ){
    $action = false;
}

$targetWitch = $this->wc->witch("target");

if( !$targetWitch->exist() ){
    $alert = [
        'level'     =>  'error',
        'message'   =>  "Witch not found"
    ];
    $this->wc->user->addAlerts([ $alert ]);
    
    header('Location: '.$this->wc->website->getRootUrl() );
    exit();
}

$structuresList = [];
$craftWitches    = null;
if( !$targetWitch->hasCraft() ){
    $structuresList = Structure::listStructures( $this->wc );
}
else 
{
    $craftWitches = $targetWitch->craft()->getWitches();
    
    foreach( $craftWitches as $key => $craftWitch )
    {
        $breadcrumb = [];
        $breadcrumbWitch    = $craftWitch->mother();
        while( !empty($breadcrumbWitch) )
        {
            $breadcrumb[]   = [
                "name"  => $breadcrumbWitch->name,
                "data"  => $breadcrumbWitch->data,
                "href"  => $this->witch->getUrl([ 'id' => $targetWitch->id ]),
            ];

            $breadcrumbWitch    = $breadcrumbWitch->mother();
        }
        
        $craftWitches[ $key ]->breadcrumb = array_reverse($breadcrumb);
        
    }
}

$alerts = $this->wc->user->getAlerts();
switch( $action )
{
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
        
        $targetWitch->reorderDaughters();
        
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
        if( $targetWitch->mother() && $targetWitch->delete() )
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Witch removed"
            ];

            $this->wc->user->addAlerts( $alerts );

            header('Location: '.$this->wc->website->getFullUrl("view?id=".$targetWitch->mother()->id) );
            exit();
        }
        
        $alerts[] = [
            'level'     =>  'error',
            'message'   =>  "Error, witch hasn't been removed",
        ];
    break;
    
    case 'delete-content':
        if( !$targetWitch->hasCraft() ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Craft hasn't been found",
            ];
        }
        elseif( !$targetWitch->removeCraft() ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, craft hasn't been removed",
            ];
        }
        else 
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Craft removed"
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
                'message'   =>  "Error, addition cancelled"
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
                'message'   =>  "Error, archiving cancelled"
            ];
        }      
    break;
    
    case 'add-position':
$this->wc->dump($_POST);
        
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
        
        $motherWitch = Witch::createFromId( $this->wc, $id);
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
            $this->wc->user->addAlerts($alerts);
            break;
        }
        else
        {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "New craft position's witch created"
            ];
            
            $this->wc->user->addAlerts($alerts);
            
            header('Location: '.$this->wc->website->getFullUrl('view?id='.$newWitchId)  );
            exit();
        }        
        
        
    break;
}

$sites  = $this->wc->website->sitesRestrictions;
if( !$sites ){
    $sites = array_keys($this->wc->configuration->sites);
}

$websitesList   = [];
foreach( $sites as $site ){
    if( $site == $this->wc->website->name ){
        $website = $this->wc->website;
    }
    else {
        $website = new Website( $this->wc, $site );
    }
    
    if( $website->site == $website->name ) {
        $websitesList[ $site ] = $website;
    }
}

$breadcrumb = [
    [
        "name"  => $targetWitch->name,
        "data"  => $targetWitch->data,
        "href"  => $this->witch->getUrl([ 'id' => $targetWitch->id ]),
    ]
];

$breadcrumbWitch    = $targetWitch->mother();
while( !empty($breadcrumbWitch) )
{
    $breadcrumb[]   = [
        "name"  => $breadcrumbWitch->name,
        "data"  => $breadcrumbWitch->data,
        "href"  => $this->witch->getUrl([ 'id' => $targetWitch->id ]),
    ];
    
    $breadcrumbWitch    = $breadcrumbWitch->mother();
}

$this->addContextVar( 'breadcrumb', array_reverse($breadcrumb) );

$this->view();