<?php /** @var WC\Module $this */

use WC\Ingredient;

$structureName = $this->wc->request->param('structure');
if( $structureName ){
    $structure = $this->wc->configuration->structure( $structureName );
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
foreach( $structures as $structureItem ){
    $possibleTypes[ $structureItem->name ] = $structureItem->name;
}



// $this->wc->debug($ingredients);
// $this->wc->debug($structures);
// $this->wc->debug($possibleTypes);
// $this->wc->dump($structure);

$alerts = $this->wc->user->getAlerts();

$this->view();