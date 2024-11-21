<?php /** @var WC\Module $this */

use WC\Tools;

if( !$this->witch("target") )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Cauldron Witch not found"
    ]);

    header( 'Location: '.$this->wc->website->getFullUrl() );
    exit();
}
elseif( !$this->witch("target")->cauldron() )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Cauldron not found"
    ]);
    
    header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$this->witch("target")->id) );
    exit();
}
elseif( !$this->witch("target")->cauldron()->draft() )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Draft can't be read"
    ]);
    
    header( 'Location: '.$this->wc->website->getFullUrl('view?id='.$this->witch("target")->id) );
    exit();
}

// TODO multi draft management
//$draft = $this->witch("target")->cauldron()?->draft();
//$this->wc->debug($this->witch("target")->cauldron());
//$this->wc->debug($draft->parent);
//$this->wc->debug($draft);

$cauldron       = $this->witch("target")->cauldron();
$return         = false;

switch( Tools::filterAction( 
    $this->wc->request->param('action'),
    [
        'save',
        'save-and-return',
        'publish',
        'delete',
    ]
) ){
    case 'publish':
        if( $cauldron->draft()->readInputs()->publish() === false ){
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, publication canceled"
            ]);
        }
        else 
        {
            $return = true;
            $this->wc->user->addAlert([
                'level'     =>  'success',
                'message'   =>  "Published"
            ]);
        }
    break;

    case 'save-and-return':
        $return = true;
    case 'save':
        //$saved = $cauldron->draft()->readInputs()->save();
        $cauldron->draft()->readInputs();
        $saved = false;

        if( $saved === false )
        {
            $return = false;
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, update canceled"
            ]);
        }
        elseif( $saved === 0 ){
            $this->wc->user->addAlert([
                'level'     =>  'warning',
                'message'   =>  "No update"
            ]);
        }
        else {
            $this->wc->user->addAlert([
                'level'     =>  'success',
                'message'   =>  "Draft Updated"
            ]);
        }        
    break;
    
    case 'delete':
        if( !$cauldron->draft()->delete() ){
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, remove canceled",
            ]);
        }
        else 
        {
            $return     = true;
            $this->wc->user->addAlert([
                'level'     =>  'success',
                'message'   =>  "Draft removed"
            ]);
        }
    break;    
}

if( $return )
{
    header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $this->witch("target")->id ]) );
    exit();
}

$this->view();