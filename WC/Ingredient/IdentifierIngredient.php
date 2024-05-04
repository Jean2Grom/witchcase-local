<?php 
namespace WC\Ingredient;

class IdentifierIngredient extends \WC\Ingredient 
{
    const TYPE  = 'identifier';

    const VALUE_FIELDS = [
        "table" =>  "value_table",
        "id"    =>  "value_id",
    ];
    

    function __toString()
    {
        if( empty($this->value['table']) ){
            return "unset";
        }
        elseif( empty($this->value['id']) ){
            return $this->value['table']." : unset";
        }

        return $this->value['table']." : ".$this->value['id'];
    }    

    /**
     * Init function used to setup ingredient
     * @param mixed $value : if left to null, read from properties values 'value'
     * @return self
     */
    function init( mixed $value=null ): self {
        return $this->set( 
            $value 
            ?? [
                'table' =>  (string) $this->properties[ 'value_table' ] ?? null,
                'id'    =>  (int) $this->properties[ 'value_id' ] ?? null,
            ] 
            ?? null 
        );
    }

    /**
     * Default function to set value
     * @param mixed $value : has to be an array with keys : 'table', string valued 
     *                                                      'id', int values
     * @return self
     */
    public function set( mixed $value )
    {
        if( !is_null($value) 
            && ( !is_array($value) 
                || !is_string($value['table'])
                || !ctype_digit(strval($value['id']))
            )
        ){
            $this->wc->log->error( "Try to set a non identifier value to ".$this->type." ingredient");
        }
        else {
            $this->value = $value;
        }

        return $this;
    }
}