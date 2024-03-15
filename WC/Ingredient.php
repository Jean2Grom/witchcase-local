<?php 
namespace WC;


class Ingredient 
{
    const DEFAULT_AVAILABLE_INGREDIENT_TYPES = [
        'boolean', 
        'cauldron_link',
        'datetime',
        'float',
        'identifier',
        'integer',
        'price',
        'string',
        'text',
    ];

    public $type;

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
    

}