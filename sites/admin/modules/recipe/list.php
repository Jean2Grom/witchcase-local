<?php /** @var WC\Module $this */

use WC\DataAccess\RecipeDataAccess;

$recipes = $this->wc->configuration->recipes();

$recipeArray = RecipeDataAccess::readUsage(
    $this->wc, 
    array_keys($recipes)
);

foreach( $recipes as $recipe ){
    $recipeArray[ $recipe->name ]['name'] = $recipe->name;
}

$this->view();