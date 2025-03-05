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

if( !$this->witch("target") )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Craft not found"
    ]);
    header( 'Location: '.$this->wc->website->getFullUrl() );
    exit();
}

$craft      = $this->witch("target")->craft() ?? false;
if( !$craft )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Craft not found"
    ]);
    header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $this->witch("target")->id ]) );
    exit();
}

// TODO multi draft management
$draft = $craft->getDraft();

if( empty($draft) ){
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Draft can't be read"
    ]);
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
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, update canceled"
            ]);
            $return = false;
        }
        elseif( $saved === 0 && !$publish ){
            $this->wc->user->addAlert([
                'level'     =>  'warning',
                'message'   =>  "No update"
            ]);
        }
        elseif( $publish )
        {
            if( $draft->publish() === false )
            {
                $this->wc->user->addAlert([
                    'level'     =>  'error',
                    'message'   =>  "Error, publication canceled"
                ]);
                
                $return = false;
            }
            else {
                $this->wc->user->addAlert([
                    'level'     =>  'success',
                    'message'   =>  "Published"
                ]);                
            }
        }
        else {
            $this->wc->user->addAlert([
                'level'     =>  'success',
                'message'   =>  "Updated"
            ]);
        }
        
        if( $return )
        {
            header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $this->witch("target")->id ]) );
            exit();
        }
    break;
    
    case 'delete':
        if( !$draft->remove() ){
            $this->wc->user->addAlert([
                'level'     =>  'error',
                'message'   =>  "Error, remove canceled",
            ]);
        }
        else 
        {
            $this->wc->user->addAlert([
                'level'     =>  'success',
                'message'   =>  "Draft removed"
            ]);            
            header( 'Location: '.$this->wc->website->getFullUrl('view', [ 'id' => $this->witch("target")->id ]) );
            exit();
        }
    break;    
}

$cancelHref = $this->wc->website->getUrl("view?id=".$this->witch("target")->id);

$this->view();