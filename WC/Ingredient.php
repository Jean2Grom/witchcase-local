<?php 
namespace WC;

use WC\Traits\DisplayTrait;

abstract class Ingredient 
{
    use DisplayTrait;

    const FIELDS = [
        "id",
        "cauldron_fk",
        "name",
        "priority",
    ];
    
    const VALUE_FIELDS = [
        "value",
    ];

    const HISTORY_FIELDS = [
        "creator",
        "created",
        "modificator",
        "modified",
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
    
    const TYPE                  = null;
    const DIR                   = "cauldron/ingredients";
    const DESIGN_SUBFOLDER      = "design/cauldron/ingredients";

    public $type;
    public $valueFields;
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
    

    function __construct()
    {
        $instanciedClass    = (new \ReflectionClass($this))->getName();
        $this->type         = $instanciedClass::TYPE;
        $this->valueFields  = $instanciedClass::VALUE_FIELDS;
    }


    function __toString()
    {
        if( is_array($this->value) )
        {
            $return     = "";
            $separator  = "";
            foreach( $this->value as $key => $value )
            {
                $return .= $separator.$key." => ".$value;
                $separator = "; ";
            }
            
            return $return;
        }

        return $this->value;
    }    

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
    function set( mixed $value )
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Reset value to null
     * @param mixed $value 
     * @return self
     */
    function reset()
    {
        $this->value = null;
        return $this;
    }

    function content( ?string $element=null ) 
    {
        if( is_null($element) ){
            return $this->value;
        }
        
        return $this->value[ $element ] ?? null;
    }

}