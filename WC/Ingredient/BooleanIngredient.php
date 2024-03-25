<?php 
namespace WC\Ingredient;

class BooleanIngredient extends \WC\Ingredient 
{
    public $type        = 'boolean';

    /**
     * Init function used to setup ingredient
     * @param mixed $value : if left to null, read from properties values 'value'
     * @return self
     */
    function init( mixed $value=null ): self {
        return $this->set( $value ?? (boolean) $this->properties[ 'value' ] ?? null );
    }

    /**
     * Set value
     * @param mixed $value : has to be a boolean
     * @return self
     */
    public function set( mixed $value )
    {
        if( !is_null($value) && !is_bool($value) ){
            $this->wc->log->error( "Try to set a non boolean value to ".$this->type." ingredient");
        }
        else {
            $this->value = $value;
        }

        return $this;
    }
}