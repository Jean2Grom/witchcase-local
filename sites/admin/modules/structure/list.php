<?php /** @var WC\Module $this */

use WC\Cauldron\Structure;
use WC\DataAccess\StructureDataAccess;

$structures = $this->wc->configuration->structures();

$structureArray = StructureDataAccess::readStructuresUsage(
    $this->wc, 
    array_keys($structures)
);

foreach( $structures as $structure )
{
    $structureArray[ $structure->name ]['name'] = $structure->type !== Structure::DEFAULT_TYPE? 
                                                            "[".$structure->type."] ": "";
    $structureArray[ $structure->name ]['name'] .= $structure->name;
}

//$this->wc->dump($structures);
//$this->wc->dump($structureArray);

$alerts = $this->wc->user->getAlerts();

$this->view();