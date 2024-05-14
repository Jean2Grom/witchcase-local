<?php 
namespace WC\Ingredient;

class StringIngredient extends \WC\Ingredient 
{
    const TYPE  = 'string';

    /**
     * Init function used to setup ingredient
     * @param mixed $value : if left to null, read from properties values 'value'
     * @return self
     */
    function init( mixed $value=null ): self {
        return $this->set( $value ?? (string) $this->properties[ 'value' ] ?? null );
    }

    /**
     * Default function to set value
     * @param mixed $value : has to be a string
     * @return self
     */
    public function set( mixed $value ): self
    {
        if( !is_null($value) && !is_string($value) ){
            $this->wc->log->error( "Try to set a non string value to ".$this->type." ingredient");
        }
        else {
            $this->value = $value;
        }

        return $this;
    }
}