<?php /** @var WC\Module $this */

use WC\DataAccess\StructureDataAccess;

$recipes = $this->wc->configuration->recipes();

$recipeArray = StructureDataAccess::readStructuresUsage(
    $this->wc, 
    array_keys($recipes)
);

foreach( $recipes as $recipe ){
    $recipeArray[ $recipe->name ]['name'] = $recipe->name;
}

$this->view();