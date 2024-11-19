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
    const DIR                   = "cauldron/ingredient";
    const DESIGN_SUBFOLDER      = "design/cauldron/ingredient";

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

    private ?string $inputID    = null;

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

    static function list(): array {
        return array_keys( self::DEFAULT_AVAILABLE_INGREDIENT_TYPES_PREFIX );
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
     * Prepare function used to write ingredient properties
     * @return self
     */
    function prepare(): self {
        if( is_scalar($this->value) ){
            $this->properties['value'] = $this->value;
        }
        else {
            $this->properties['value'] = null;
        }
        
        return $this;
    }

    /**
     * Default function to set value
     * @param mixed $value 
     * @return self
     */
    function set( mixed $value ): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Reset value to null
     * @param mixed $value 
     * @return self
     */
    function reset(): self
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

    function save(): bool
    {
        if( !$this->exist() )
        {
            Handler::writeProperties($this);
            $result = DataAccess::insert($this);            
            if( $result ){
                $this->id = (int) $result;
            }
        }
        else 
        {
            $properties = $this->properties;
            Handler::writeProperties( $this );
            $result = DataAccess::update( $this, array_diff_assoc($this->properties, $properties) );
        }
        
        $this->inputID = null;

        return $result !== false;
    }

    function delete(): bool
    {
        if( $this->exist() ){
            return DataAccess::delete( $this ) !== false;
        }

        return true;
    }

    function readInputs( mixed $input ): self 
    {
        if( !empty($input['name']) ){
            $this->name = htmlspecialchars($input['name']);
        }

        if( isset($input['priority']) && is_int($input['priority']) ){
            $this->priority = $input['priority'];
        }

        return $this->readInput( $input['value'] );
    }

    function readInput( mixed $input ): self {        
        return $this->set( $input );
    }

    function getInputIdentifier(): string 
    {
        if( $this->inputID ){
            return $this->inputID;
        }

        $this->inputID = str_replace( ' ', '-', $this->name).'#';

        if( $this->cauldron ){
            $this->inputID .= array_keys(array_intersect(
                $this->cauldron->ingredients, 
                [$this]
            ))[0] ?? "";
        }

        return $this->inputID;
    }

    function getInputIndex()
    {
        return array_keys(array_intersect(
            $this->cauldron?->contents() ?? [], 
            [$this]
        ))[0] ?? 0;
    }

    function isIngredient(): bool {
        return true;
    }

}