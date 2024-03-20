<?php 
namespace WC\Handler;

use WC\Cauldron;
use WC\Ingredient;

//use WC\DataAccess\Cauldron AS CauldronDA;
//use WC\Datatype\ExtendedDateTime;

class IngredientHandler
{
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

    /**
     * @param Cauldron $cauldron
     * @param array $row
     * @return Ingredient
     */
    static function createFromData(  Cauldron $cauldron, array $row ): Ingredient
    {
        $ingredient             = new Ingredient();
        $ingredient->cauldron   = $cauldron;
        $ingredient->wc         = $cauldron->wc;
        
        foreach( self::DEFAULT_AVAILABLE_INGREDIENT_TYPES_PREFIX as $type => $prefix ){
            if( !empty($row[ $prefix.'_id' ]) 
                && empty($cauldron->ingredients[ $row[$prefix.'_id'] ]) )
            {
                $className = "\\WC\\Ingredient\\".ucfirst($type).'Ingredient';
                
                if( !class_exists($className) )
                {
                    $cauldron->wc->debug( "Ingredient ".$type." : class not found \"".$className."\", skip" );
                    continue;
                }

                $ingredient             = new $className();
                $ingredient->cauldron   = $cauldron;
                $ingredient->wc         = $cauldron->wc;
                
                

            }
            //$cauldron->properties[ $field ] = $data[ $field ] ?? null;
        }
        
        self::readProperties( $cauldron );
        
        $cauldron->position    = [];
        
        $i = 1;
        while( isset($data['level_'.$i]) )
        {
            $cauldron->position[$i] = (int) $data['level_'.$i];
            $i++;
        }
        $cauldron->depth       = $i - 1; 
        
        if( $cauldron->depth == 0 ){
            $cauldron->parent = false;
        }
        
        return $ingredient;
    }

    /**
     * Update Object properties based of object var "properties"
     * @return void
     */
    static function readProperties( Cauldron $cauldron ): void
    {
        if( !empty($cauldron->properties['id']) ){
            $cauldron->id = (int) $cauldron->properties['id'];
        }
        
        if( isset($cauldron->properties['status']) ){
            $cauldron->status = $cauldron->properties['status'] === 0? 'draft': 'archive';
        }
        
        if( !empty($cauldron->properties['content_key']) ){
            $cauldron->contentID = (int) $cauldron->properties['content_key'];
        }
        
        if( !empty($cauldron->properties['name']) ){
            $cauldron->name = $cauldron->properties['name'];
        }
        
        if( !empty($cauldron->properties['data']) ){
            $cauldron->data = json_decode( $cauldron->properties['data'], true );
        }
        
        if( !empty($cauldron->properties['datetime']) ){
            $cauldron->datetime = new ExtendedDateTime($cauldron->properties['datetime']);
        }
                
        return;
    }
        

}