<?php 
namespace WC\Handler;

use WC\Cauldron;
use WC\Ingredient;

//use WC\Datatype\ExtendedDateTime;

class IngredientHandler
{
    /**
     * @param Cauldron $cauldron
     * @param array $row
     * @return void
     */
    static function createFromData(  Cauldron $cauldron, array $row ): void
    {
        foreach( Ingredient::DEFAULT_AVAILABLE_INGREDIENT_TYPES_PREFIX as $type => $prefix )
        {
            $id = $row[ $prefix.'_id' ] ?? null;
            if( is_null($id) ){
                continue;
            }

            foreach( $cauldron->ingredients[ $type ] ?? [] as $cauldronTypeIngredient ){
                if( $cauldronTypeIngredient->id == $id ){
                    continue 2;
                }
            }
            
            $className  =   "\\WC\\Ingredient\\";
            $className  .=  str_replace('_', '', ucwords($type, '_'));
            $className  .=  'Ingredient';
            
            if( !class_exists($className) )
            {
                $cauldron->wc->debug( "Ingredient ".$type." : class not found \"".$className."\", skip" );
                continue;
            }

            $ingredient = new $className();
            foreach( Ingredient::FIELDS as $field ){
                $ingredient->properties[ $field ] = $row[ $prefix.'_'.$field ] ?? null;
            }
            foreach( $ingredient->valueFields as $field ){
                $ingredient->properties[ $field ] = $row[ $prefix.'_'.$field ] ?? null;
            }

            $ingredient->cauldron           = $cauldron;
            $cauldron->ingredients[ $type ] = array_replace(
                $cauldron->ingredients[ $type ] ?? [], 
                [ $id => $ingredient ]
            );

            self::readProperties( $ingredient );
        }
        
        return;
    }

    /**
     * Update Object properties based of object var "properties"
     * @return void
     */
    static function readProperties( Ingredient $ingredient ): void
    {
        if( !empty($ingredient->properties['id']) ){
            $ingredient->id = (int) $ingredient->properties['id'];
        }

        if( !empty($ingredient->cauldron) )
        {
            $ingredient->wc                         = $ingredient->cauldron->wc;
            $ingredient->properties['cauldron_fk']  = $ingredient->cauldron->id;
        }

        if( !empty($ingredient->properties['name']) ){
            $ingredient->name = $ingredient->properties['name'];
        }
        
        if( !empty($ingredient->properties['priority']) ){
            $ingredient->priority = (int) $ingredient->properties['priority'];
        }

        $ingredient->init();
        
        return;
    }
    

    static function getTypeFields( string $type ): array 
    {
        $className  =   "\\WC\\Ingredient\\";
        $className  .=  str_replace('_', '', ucwords($type, '_'));
        $className  .=  'Ingredient';
        
        if( !class_exists($className) ){
            return [];
        }

        $return = [];
        foreach( Ingredient::FIELDS as $field ){
            $return[] = $field;
        }

        $ingredient = new $className(); 
        foreach( $ingredient->valueFields as $field ){
            $return[] = $field;
        }

        return $return;
    }
}