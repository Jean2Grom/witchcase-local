<?php 
namespace WC;

use WC\Trait\CauldronContentTrait;
use WC\Handler\IngredientHandler as Handler;
use WC\DataAccess\IngredientDataAccess as DataAccess;

abstract class Ingredient 
{
    use CauldronContentTrait;

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

    public string $type;
    public array $valueFields;
    public $value;

    public array $properties = [];

    public ?int $id;
    public ?int $cauldronID;
    public ?string $name;
    public ?int $priority;

    public ?int $creator;
    public ?\DateTime $created;
    public ?int $modificator;
    public ?\DateTime $modified;

    /** 
     * Cauldron witch contains this ingredient
     * @var ?Cauldron
     */
    public ?Cauldron $cauldron = null;
    
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
     * Is this ingredient exist in database ?
     * @return bool
     */
    function exist(): bool {
        return !empty($this->id);
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
     * write values to properties value
     * @return self
     */
    function prepare(): self 
    {
        if( !is_array($this->value) )
        {
            $this->properties[ array_values($this->valueFields)[0] ] = $this->value;
            return $this;
        }

        $elementMap = [];
        foreach( $this->valueFields as $key => $valueField ){
            if( is_int($key) ){
                $elementMap[ $valueField ] = $valueField;
            }
            else {
                $elementMap[ $key ] = $valueField;
            }
        }

        foreach( $this->content() as $element => $value ){
            $this->properties[ $elementMap[$element] ] = $value;
        } 

        return $this;
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


    function save(): self
    {
        if( !$this->exist() )
        {
            Handler::writeProperties($this);
            DataAccess::insert($this);
        }
        else 
        {
            $properties = $this->properties;
            Handler::writeProperties($this);
            DataAccess::update($this, array_diff_assoc($properties, $this->properties));
        }
        
        return $this;
    }
}