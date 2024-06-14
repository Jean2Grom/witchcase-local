<?php /** @var WC\Module $this */

use WC\DataAccess\StructureDataAccess;

$structures = $this->wc->configuration->structures();

$structureArray = StructureDataAccess::readStructuresUsage(
    $this->wc, 
    array_keys($structures)
);

foreach( $structures as $structure ){
    $structureArray[ $structure->name ]['name'] = $structure->name;
}

$alerts = $this->wc->user->getAlerts();

$this->view();