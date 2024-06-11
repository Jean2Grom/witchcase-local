<?php /** @var WC\Module $this */

use WC\DataAccess\StructureDataAccess;

$structures = $this->wc->configuration->structures();

$structureArray = StructureDataAccess::readStructuresUsage(
    $this->wc, 
    array_keys($structures)
);

foreach( $structures as $structureData )
{
    $structureArray[ $structureData['name'] ]['name'] = $structureData['type'] !== "structure"? 
                                                            "[".$structureData['type']."] ": "";
    $structureArray[ $structureData['name'] ]['name'] .= $structureData['name'];
}

$this->wc->dump($structures);
$this->wc->dump($structureArray);

$alerts = $this->wc->user->getAlerts();

$this->view();