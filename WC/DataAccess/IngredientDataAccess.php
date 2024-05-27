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
        if( count($params) === 0 ){
            return 0;
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

    static function delete( Ingredient $ingredient )
    {
        if( empty($ingredient->id) ){
            return false;
        }
        
        $query = "";
        $query  .=  "DELETE FROM `".IngredientHandler::table($ingredient)."` ";
        $query  .=  "WHERE `id` = :id ";

        return $ingredient->wc->db->deleteQuery( $query,  ['id' => $ingredient->id] );
    }
}