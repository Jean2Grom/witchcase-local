<?php 
namespace WC\Ingredient;

class IdentifierIngredient extends \WC\Ingredient 
{
    public $type        = 'identifier';
    public $valueFields = [ 'value_table', 'value_id' ];


    function readFromProperty(): self
    {
        $this->value = [
            'table' =>  $this->properties[ 'value_table' ] ?? null,
            'id'    =>  $this->properties[ 'value_id' ] ?? null,
        ];
        return $this;
    }

}