<?php
namespace WC\DataAccess;

use WC\WitchCase;
use WC\Cauldron;
use WC\Ingredient;
use WC\Handler\IngredientHandler;

class IngredientDataAccess
{
    static function insert( Ingredient $ingredient )
    {
        if( isset($ingredient->properties['id']) ){
            unset($ingredient->properties['id']);
        }
        
        $query = "";
        $query  .=  "INSERT INTO `".IngredientHandler::table($ingredient)."` ";
        
        $separator = "( ";
        foreach( array_keys($ingredient->properties) as $field )
        {
            $query  .=  $separator."`".$field."` ";
            $separator = ", ";
        }
        $query  .=  ") VALUES ";
        
        $separator = "( ";
        foreach( array_keys($ingredient->properties) as $field )
        {
            $query  .=  $separator.":".$field." ";
            $separator = ", ";
        }
        $query  .=  ") ";
        
        return $ingredient->wc->db->insertQuery($query, $ingredient->properties);
    }


    static function update( Ingredient $ingredient, array $params )
    {
        if( empty($params) ){
            return false;
        }
        
        $query = "";
        $query  .=  "UPDATE `".IngredientHandler::table($ingredient)."` ";
        
        $separator = "SET ";
        foreach( array_keys($params) as $field )
        {
            $query      .=  $separator.'`'.$ingredient->wc->db->escape_string($field)."` = :".$field." ";
            $separator  =  ", ";
        }
        
        $query  .=  "WHERE `id` = :id ";

        return $ingredient->wc->db->updateQuery( $query, array_replace($params, [ 'id' => $ingredient->id ]) );
    }


}