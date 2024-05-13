<?php 
namespace WC;

use WC\Trait\CauldronIngredientTrait;
use WC\Handler\IngredientHandler as Handler;
use WC\DataAccess\IngredientDataAccess as DataAccess;

abstract class Ingredient 
{
    use CauldronIngredientTrait;

    const FIELDS = [
        "id",
        "cauldron_fk",
        "name",
        "value",
        "priority",
    ];
    
    const HISTORY_FIELDS = [
        "creator",
        "created",
        "modificator",
        "modified",
    ];

    const DEFAULT_AVAILABLE_INGREDIENT_TYPES_PREFIX = [
        'boolean'       => 'b', 
        'datetime'      => 'dt', 
        'float'         => 'f', 
        'integer'       => 'i', 
        'price'         => 'p', 
        'string'        => 's', 
        'text'          => 't', 
    ];
    
    const TYPE                  = null;
    const DIR                   = "cauldron/ingredients";
    const DESIGN_SUBFOLDER      = "design/cauldron/ingredients";

    public string $type;
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

    public string $editPrefix   = "i";

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
    }


    function __toString(){
        return $this->value ?? ( $this->id? ($this->name ?? $this->type).": ".$this->id : "" );
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

    function readInput( ?string $caller=null, /*?int $callerIndice=null*/array $callerIndices=[] )
    {
        //$prefix     = $caller? $caller."|": "";
        //$this->wc->debug( $callerIndices, $prefix.$this->getInputName(false) );
        //$this->wc->debug( $callerIndice, $caller );
        //$this->wc->debug( $callerIndices, $caller );
        
        $this->wc->debug( $this->wc->request->param( $caller ) );

        return;
    }

}