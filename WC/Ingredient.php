<?php 
namespace WC;


class Ingredient 
{
    const FIELDS = [
        "id",
        "cauldron_fk",
        "name",
        "priority",
        "creator",
        "created",
        "modificator",
        "modified",
    ];
    
    public $valueFields = [
        "value",
    ];

    const DEFAULT_AVAILABLE_INGREDIENT_TYPES_PREFIX = [
        'boolean'       => 'b', 
        'cauldron_link' => 'cl', 
        'datetime'      => 'dt', 
        'float'         => 'f', 
        'identifier'    => 'identifier', 
        'integer'       => 'i', 
        'price'         => 'p', 
        'string'        => 's', 
        'text'          => 't', 
    ];
    
    public $type;
    public $value;

    public $properties;

    public $id;
    public $name;
    public $priority;

    public $creator;
    public $created;
    public $modificator;
    public $modified;

    /** 
     * Cauldron witch contains this ingredient
     * @var Cauldron
     */
    public Cauldron $cauldron;
    
    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;
    
    /**
     * Init function used to setup ingredient
     * @param mixed $value : if left to null, read from properties values 'value'
     * @return self
     */
    function init( mixed $value=null ): self {
        return $this->set( $value ?? $this->properties[ 'value' ] ?? null );
    }

    /**
     * Default function to set value
     * @param mixed $value 
     * @return self
     */
    public function set( mixed $value )
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Reset value to null
     * @param mixed $value 
     * @return self
     */
    public function reset()
    {
        $this->value = null;
        return $this;
    }
}