<?php /** @var WC\Module $this */

//use WC\DataAccess\StructureDataAccess;

$structureName = $this->wc->request->param('structure');
if( $structureName ){
    $structure = $this->wc->configuration->structure( $this->wc->request->param('structure') );
}

if( !$structure )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Structure not found"
    ]);
    header( 'Location: '.$this->wc->website->getFullUrl('structure') );
    exit();
}

$this->wc->dump($structure);

$alerts = $this->wc->user->getAlerts();

$this->view();