<?php 
namespace WC;


class Ingredient 
{
    public $type;
    public $value;

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
     * Default function to set value
     * @param mixed $value 
     * @return self
     */
    public function set( mixed $value )
    {
        $this->value = $value;
        return $this;
    }
}