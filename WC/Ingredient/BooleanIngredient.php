<?php 
namespace WC\Ingredient;

class BooleanIngredient extends \WC\Ingredient 
{
    public $type        = 'boolean';

    function readFromProperty(): self
    {
        $this->value = (boolean) $this->properties[ 'value' ] ?? null;
        return $this;
    }

}