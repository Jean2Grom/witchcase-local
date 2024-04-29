<?php
namespace WC\DataAccess;

use WC\WitchCase;
use WC\Cauldron as CauldronObj;
use WC\Ingredient;
use WC\Handler\IngredientHandler;

class Cauldron
{    
    const RELATIONSHIPS_JOINTURE = [
        'siblings' => "siblingsJointure",
        'parents'  => "parentsJointure",
        'children' => "childrenJointure",
    ];

    static function getDepth( WitchCase $wc, bool $useCache=true ): int
    {
        if( $useCache ){
            $depth = $wc->cache->read( 'system', 'depth-cauldron' );
        }
        
        if( empty($depth) )
        {
            $query  =   "SHOW COLUMNS FROM `cauldron` WHERE `Field` LIKE 'level_%'";
            $result =   $wc->db->selectQuery($query);
            $depth  =   count($result);
            
            if( $useCache ){
                $wc->cache->create('system', 'depth-cauldron', $depth);
            }
        }
        
        return (int) $depth;
    }

    static function cauldronRequest( WitchCase $wc, array $configuration )
    {
        // Determine the list of fields in select part of query
        $query = "";
        $separator = "SELECT DISTINCT ";
        foreach( CauldronObj::FIELDS as $field )
        {
            $query      .=  $separator."`c`.`".$field."` ";
            $separator  =   ", ";
        }
        for( $i=1; $i<=$wc->cauldronDepth; $i++ ){
            $query      .=  $separator."`c`.`level_".$i."` ";
        }
        
        $excludFields = [
            'cauldron_fk',
        ];
        foreach( Ingredient::DEFAULT_AVAILABLE_INGREDIENT_TYPES_PREFIX as $type => $prefix ){
            foreach( IngredientHandler::getTypeFields( $type ) as $field ){
                if( !in_array($field, $excludFields) ){
                    $query  .=  ", `".$prefix."`.`".$field."` AS `".$prefix."_".$field."` ";
                }
            }
        }
        
        $query  .= "FROM ";

        $userConnexionJointure = false;
        if( in_array('user', $configuration) && $wc->user->connexion )
        {
            $userConnexionJointure = true;
            $query  .= "`ingredient__identifier` AS `user_connexion`, ";
        }
        
        $query  .= "`cauldron` AS `c` ";
        foreach( Ingredient::DEFAULT_AVAILABLE_INGREDIENT_TYPES_PREFIX as $type => $prefix )
        {
            $query  .=  "LEFT JOIN `ingredient__".$type."` AS `".$prefix."` ";
            $query  .=      "ON `".$prefix."`.`cauldron_fk` = `c`.`id` ";
        }

        foreach( $configuration as $conf )
        {
            $query  .= "LEFT JOIN `cauldron` AS `c_".$conf."` ";
            $query  .=  "ON ( ";
            $query  .=  self::childrenJointure( $wc->cauldronDepth, "c_".$conf, 'c', '*' );
            $query  .=  ") ";            
        }

        
        $parameters = [];
        $separator = "WHERE ( ";
        
        foreach( $configuration as $conf )
        {
            if( ctype_digit(strval($conf)) )
            {
                $parameters[ 'c_'.$conf ]    = (int) $conf;
    
                $condition  =   " %s.`id` = :c_".$conf." ";
    
                $query      .=  $separator;
                $separator  =   "OR ";

                $query      .=  str_replace(' %s.', ' `c`.', $condition);
                $query      .=  " OR ".str_replace(' %s.', " `c_".$conf."`.", $condition);
            }
        }
        
        if( $userConnexionJointure )
        {
            $query      .=  $separator;
            $separator  =   "OR ";

            $query  .=  "( ";
            $query  .=      " `c`.`id` = `user_connexion`.`cauldron_fk` ";
            $query  .=          "OR  `c_user`.`id` = `user_connexion`.`cauldron_fk` ";
            $query  .=  ") ";

            $query  .=  "AND `user_connexion`.`id` IS NOT NULL ";
            $query  .=  "AND `user_connexion`.`value_table` = \"user__connexion\" ";
            $query  .=  "AND `user_connexion`.`value_id` = :user_id ";

            $parameters[ 'user_id' ] = (int) $wc->user->id;
        }
        
        $query .=  ") ";
        
        return $wc->db->selectQuery($query, $parameters);
    }


    private static function childrenJointure( $maxDepth, $mother, $daughter, $depth=1 )
    {
        $m = function (int $level) use ($mother): string {
            return "`".$mother."`.`level_".$level."`";
        };
        $d = function (int $level) use  ($daughter): string {
            return "`".$daughter."`.`level_".$level."`";
        };
        
        $jointure = "( `".$mother."`.`id` <> `".$daughter."`.`id` ) ";
        
        $jointure  .=      "AND ( ";
        $jointure  .=          "( ".$m(1)." IS NOT NULL AND ".$d(1)." = ".$m(1)." ) ";
        $jointure  .=          "OR ( ".$m(1)." IS NULL AND ".$d(1)." IS NOT NULL ) ";
        $jointure  .=      ") ";
        
        for( $i=2; $i <= $maxDepth; $i++ )
        {
            $jointure  .=  "AND ( ";
            $jointure  .=      "( ".$m($i)." IS NOT NULL AND ".$d($i)." = ".$m($i)." ) ";
            $jointure  .=      "OR ( ".$m($i)." IS NULL AND ".$m($i-1)." IS NOT NULL AND ".$d($i)." IS NOT NULL ) ";
            $jointure  .=      "OR (  ".$m($i)." IS NULL AND ".$m($i-1)." IS NULL ";
            // Apply level
            if( $depth != '*' && ($depth + $i - 1) <= $maxDepth ){
                $jointure  .=       "AND ".$d($depth + $i - 1)." IS NULL ";
            }
            $jointure  .=      ") ";
            $jointure  .=  ") ";
        }
        
        return $jointure;
    }
    
    private static function parentsJointure( $maxDepth, $daughter, $mother, $depth=1 )
    {
        return self::childrenJointure( $maxDepth, $mother, $daughter, $depth );
    }


    private static function siblingsJointure( $maxDepth, $witch, $sister, $depth=1 )
    {
        $w = function (int $level) use ($witch): string {
            return "`".$witch."`.`level_".$level."`";
        };
        $s = function (int $level) use  ($sister): string {
            return "`".$sister."`.`level_".$level."`";
        };
        
        $jointure = "( `".$witch."`.`id` <> `".$sister."`.`id` ) ";
        
        for( $i=1; $i < $maxDepth; $i++ )
        {
            $jointure  .=  "AND ( ";
            $jointure  .=      "( ".$w($i)." IS NOT NULL AND ".$w($i+1)." IS NOT NULL AND ".$s($i)." = ".$w($i)." ) ";
            $jointure  .=      "OR ( ".$w($i)." IS NOT NULL AND ".$w($i+1)." IS NULL AND ".$s($i)." IS NOT NULL ) ";
            
            if( $i == 1 ){
                $jointure  .=      "OR ( ".$w($i)." IS NULL AND ".$s($i)." IS NULL ) ";
            }
            elseif( $depth != '*' && ($i + 1 - $depth) > 0 )
            {
                $jointure  .=      "OR ( ".$w($i)." IS NULL AND ".$w($i + 1 - $depth)." IS NULL AND ".$s($i)." IS NULL ) ";
                $jointure  .=      "OR ( ".$w($i)." IS NULL AND ".$w($i + 1 - $depth)." IS NOT NULL ) ";
                
            }
            else {
                $jointure  .=      "OR ( ".$w($i)." IS NULL ) ";
            }
            
            $jointure  .=  ") ";
        }
        
        $jointure  .=      "AND ( ";
        $jointure  .=          "( ".$w($maxDepth)." IS NOT NULL AND ".$s($maxDepth)." IS NOT NULL ) ";
        if( $depth != '*' && ($maxDepth + 1 - $depth) > 0 )
        {
            $jointure  .=          "OR ( ".$w($maxDepth)." IS NULL AND ".$w($maxDepth + 1 - $depth)." IS NULL AND ".$s($maxDepth)." IS NULL ) ";
            $jointure  .=          "OR ( ".$w($maxDepth)." IS NULL AND ".$w($maxDepth + 1 - $depth)." IS NOT NULL ) ";
        }
        else {
            $jointure  .=      "OR ( ".$w($maxDepth)." IS NULL ) ";
        }
        $jointure  .=      ") ";
        
        return $jointure;
    }


    static function increaseDepth( WitchCase $wc ): int
    {
        $wc->cache->delete( 'system', 'depth-cauldron' );
        $newLevelDepth = self::getDepth($wc, false) + 1;
        
        $query  =   "ALTER TABLE `cauldron` ";
        $query  .=  "ADD `level_".$newLevelDepth."` INT(11) UNSIGNED NULL DEFAULT NULL ";
        $query  .=  ", ADD KEY `IDX_level_".$newLevelDepth."` (`level_".$newLevelDepth."`) ";
        
        $wc->db->alterQuery($query);
        $wc->cauldronDepth = $newLevelDepth;
        
        return $newLevelDepth;
    }

    static function getNewPosition( CauldronObj $cauldron )
    {
        $depth = count($cauldron->position) + 1;
        
        $params = [];
        $query  = "SELECT MAX(`level_".$depth."`) AS `maxIndex` FROM `cauldron` ";
        
        $linkingCondition = "WHERE ";
        foreach( $cauldron->position as $level => $levelPosition )
        {
            $field              =   "level_".$level;
            $query              .=  $linkingCondition."`".$field."` = :".$field." ";
            $params[ $field ]   =   $levelPosition;
            $linkingCondition   =   "AND ";
        }
        
        $result = $cauldron->wc->db->fetchQuery($query, $params);
        
        if( !$result ){
            return false;
        }
        
        $max = (int) $result["maxIndex"];
        
        return $max + 1;
    }

    static function insert( CauldronObj $cauldron )
    {
        if( isset($cauldron->properties['id']) ){
            unset($cauldron->properties['id']);
        }
        if( isset($cauldron->properties['datetime']) ){
            unset($cauldron->properties['datetime']);
        }
        
        $query = "";
        $query  .=  "INSERT INTO `cauldron` ";
        
        $separator = "( ";
        foreach( array_keys($cauldron->properties) as $field )
        {
            $query  .=  $separator."`".$field."` ";
            $separator = ", ";
        }
        $query  .=  ") VALUES ";
        
        $separator = "( ";
        foreach( array_keys($cauldron->properties) as $field )
        {
            $query  .=  $separator.":".$field." ";
            $separator = ", ";
        }
        $query  .=  ") ";
        
        return $cauldron->wc->db->insertQuery($query, $cauldron->properties);
    }


    // TODO
    static function update( WitchCase $wc, array $params, array $conditions )
    {
        if( empty($params) || empty($conditions) ){
            return false;
        }
        
        $query = "";
        $query  .=  "UPDATE `cauldron` ";
        
        $separator = "SET ";
        foreach( array_keys($params) as $field )
        {
            $query      .=  $separator.'`'.$wc->db->escape_string($field)."` = :".$field." ";
            $separator  =  ", ";
        }
        
        $separator = "WHERE ";
        foreach( array_keys($conditions) as $field )
        {
            $query      .=  $separator.'`'.$wc->db->escape_string($field)."` = :".$field." ";
            $separator  =  "AND ";
        }
        
        return $wc->db->updateQuery( $query, array_replace($params, $conditions) );
    }


}