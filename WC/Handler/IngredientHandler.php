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
     * @return Ingredient
     */
    static function createFromData(  Cauldron $cauldron, array $row ): void
    {
        $ingredient             = new Ingredient();
        $ingredient->cauldron   = $cauldron;
        $ingredient->wc         = $cauldron->wc;
        
        foreach( Ingredient::DEFAULT_AVAILABLE_INGREDIENT_TYPES_PREFIX as $type => $prefix )
        {
            $id = $row[ $prefix.'_id' ] ?? null;
            if( is_null($id) ){
                continue;
            }

            $alreadyCreated = false;
            foreach( $cauldron->ingredients as $cauldronIngredientItem ){
                if( $cauldronIngredientItem->id == $id )
                {
                    $alreadyCreated = true;
                    break;
                }
            }

            if( $alreadyCreated ){
                continue;
            }

            $className = "\\WC\\Ingredient\\".ucfirst($type).'Ingredient';
            
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

            $ingredient->cauldron                   = $cauldron;

            self::readProperties( $ingredient );

            //$cauldron->wc->debug( $ingredient->getValueFields(), 'la', 2 );
            $cauldron->wc->debug( $ingredient, 'ici', 2 );

            $cauldron->ingredients[] = $ingredient;
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

        $ingredient->readFromProperty();
        
        return;
    }
        

}