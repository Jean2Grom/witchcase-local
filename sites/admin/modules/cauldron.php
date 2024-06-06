<?php /** @var WC\Module $this */

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

$alerts = $this->wc->user->getAlerts();

if( is_null($this->witch("target")) )
{
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Cauldron Witch not found"
    ];
    
    $this->wc->user->addAlerts($alerts);
    header( 'Location: '.$this->wc->website->getFullUrl() );
    exit();
}
elseif( !$this->witch("target")->cauldron() )
{
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Cauldron not found"
    ];
    
    $this->wc->user->addAlerts($alerts);
    header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$this->witch("target")->id) );
    exit();
}

// TODO multi draft management
$draft = $this->witch("target")->cauldron()?->draft();

if( is_null($draft) ){
    $alerts[] = [
        'level'     =>  'error',
        'message'   =>  "Draft can't be read"
    ];
}

$return = false;
switch( $action )
{
    case 'publish':
        if( $draft->readInputs()->publish() === false ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, publication canceled"
            ];            
        }
        else 
        {
            $return     = true;
            $alerts[]   = [
                'level'     =>  'success',
                'message'   =>  "Published"
            ];                
        }
    break;

    case 'save-and-return':
        $return = true;
    case 'save':
        $saved = $draft->readInputs()->save();

        if( $saved === false )
        {
            $return     = false;
            $alerts[]   = [
                'level'     =>  'error',
                'message'   =>  "Error, update canceled"
            ];
        }
        elseif( $saved === 0 ){
            $alerts[] = [
                'level'     =>  'warning',
                'message'   =>  "No update"
            ];
        }
        else {
            $alerts[] = [
                'level'     =>  'success',
                'message'   =>  "Draft Updated"
            ];
        }        
    break;
    
    case 'delete':
        if( !$draft->delete() ){
            $alerts[] = [
                'level'     =>  'error',
                'message'   =>  "Error, remove canceled",
            ];
        }
        else 
        {
            $return     = true;
            $alerts[]   = [
                'level'     =>  'success',
                'message'   =>  "Draft removed"
            ];
        }
    break;    
}

if( $return )
{
    $this->wc->user->addAlerts($alerts);

    header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$this->witch("target")->id) );
    exit();
}


$cancelHref = $this->wc->website->getUrl("view?id=".$this->witch("target")->id);

foreach( $this->wc->user->getAlerts() as $treatmentAlert ){
    $alerts[] = $treatmentAlert;
}

$this->view();