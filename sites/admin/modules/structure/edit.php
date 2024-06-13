<?php /** @var WC\Module $this */

use WC\Ingredient;
use WC\Cauldron\Structure;

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

$structures     = $this->wc->configuration->structures();
$ingredients    = Ingredient::list();

$possibleTypes = [];
foreach( $ingredients as $ingredient ){
    $possibleTypes[ $ingredient ] = $ingredient;
}
foreach( $structures as $structureItem )
{
    $possibleTypes[ $structureItem->name ] = $structureItem->type !== Structure::DEFAULT_TYPE? 
                                            '['.$structureItem->type.'] ': 
                                            '';
    $possibleTypes[ $structureItem->name ] .= $structureItem->name;
}



// $this->wc->debug($ingredients);
// $this->wc->debug($structures);
// $this->wc->debug($possibleTypes);
// $this->wc->dump($structure);

$alerts = $this->wc->user->getAlerts();

$this->view();