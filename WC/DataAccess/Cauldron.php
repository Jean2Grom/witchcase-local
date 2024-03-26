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

    static function getDepth( WitchCase $wc ): int
    {
        $depth = $wc->cache->read( 'system', 'depth-cauldron' );
        
        if( empty($depth) )
        {
            $query  =   "SHOW COLUMNS FROM `cauldron` WHERE `Field` LIKE 'level_%'";
            $result =   $wc->db->selectQuery($query);
            $depth  =   count($result);
            
            $wc->cache->create('system', 'depth-cauldron', $depth);
        }
        
        return (int) $depth;
    }

    static function cauldronRequest( WitchCase $wc, array $configuration )
    {
        $userConnexionJointure = !empty($configuration['user']) && $wc->user->connexion;
        
        // Determine the list of fields in select part of query
        $query = "";
        $separator = "SELECT DISTINCT ";
        foreach( CauldronObj::FIELDS as $field )
        {
            $query      .=  $separator."`c`.`".$field."` ";
            $separator  =   ", ";
        }
        for( $i=1; $i<=$wc->caudronDepth; $i++ ){
            $query      .=  $separator."`c`.`level_".$i."` ";
        }
        
        $excludFields = [
            'cauldron_fk',
            'creator',
            'created',
            'modificator',
            'modified',
        ];
        foreach( Ingredient::DEFAULT_AVAILABLE_INGREDIENT_TYPES_PREFIX as $type => $prefix ){
            foreach( IngredientHandler::getTypeFields( $type ) as $field ){
                if( !in_array($field, $excludFields) ){
                    $query  .=  ", `".$prefix."`.`".$field."` AS `".$prefix."_".$field."` ";
                }
            }
        }
        
        $query  .= "FROM ";
        if( $userConnexionJointure ){
            $query  .= "`ingredient__identifier` AS `user_connexion`, ";
        }
        
        $query  .= "`cauldron` AS `c` ";

        foreach( Ingredient::DEFAULT_AVAILABLE_INGREDIENT_TYPES_PREFIX as $type => $prefix )
        {
            $query  .=  "LEFT JOIN `ingredient__".$type."` AS `".$prefix."` ";
            $query  .=      "ON `".$prefix."`.`cauldron_fk` = `c`.`id` ";
        }

/*
        $query  .= "LEFT JOIN `ingredient__boolean` AS `b` ";
        $query  .=      "ON `b`.`cauldron_fk` = `c`.`id` ";
        
        $query  .= "LEFT JOIN `ingredient__datetime` AS `dt` ";
        $query  .=      "ON `dt`.`cauldron_fk` = `c`.`id` ";

        $query  .= "LEFT JOIN `ingredient__float` AS `f` ";
        $query  .=      "ON `f`.`cauldron_fk` = `c`.`id` ";

        $query  .= "LEFT JOIN `ingredient__integer` AS `i` ";
        $query  .=      "ON `i`.`cauldron_fk` = `c`.`id` ";

        $query  .= "LEFT JOIN `ingredient__price` AS `p` ";
        $query  .=      "ON `p`.`cauldron_fk` = `c`.`id` ";

        $query  .= "LEFT JOIN `ingredient__string` AS `s` ";
        $query  .=      "ON `s`.`cauldron_fk` = `c`.`id` ";
        
        $query  .= "LEFT JOIN `ingredient__text` AS `t` ";
        $query  .=      "ON `t`.`cauldron_fk` = `c`.`id` ";

        $query  .= "LEFT JOIN `ingredient__identifier` AS `identifier` ";
        $query  .=      "ON `identifier`.`cauldron_fk` = `c`.`id` ";
        
        $query  .= "LEFT JOIN `ingredient__cauldron_link` AS `cl` ";
        $query  .=      "ON `cl`.`cauldron_fk` = `c`.`id` ";
*/
        $leftJoin = [];
        foreach( $configuration as $type => $typeConfiguration ) 
        {
            if( $type === 'user' ){
                $cauldronRefConfJoins = [ 'user' => $typeConfiguration ];
            }
            elseif( $type === 'id' && isset($typeConfiguration['id']) ){
                $cauldronRefConfJoins = [ 'id_'.$typeConfiguration['id'] => $typeConfiguration ];
            }
            elseif( $type === 'id' )
            {
                $cauldronRefConfJoins = [];
                foreach( $typeConfiguration as $typeConfigurationItem ){
                    $cauldronRefConfJoins[ 'id_'.$typeConfigurationItem['id'] ] = $typeConfigurationItem;
                }
            }
            
            foreach( $cauldronRefConfJoins as $cauldronRef => $cauldronRefConf ) 
            {
                $leftJoin[ $cauldronRef ] = false;
                foreach( array_keys(self::RELATIONSHIPS_JOINTURE) as $relationship ){
                    if( !empty($cauldronRefConf[ $relationship ]) )
                    {
                        $leftJoin[ $cauldronRef ] = true;
                        break;
                    }
                }

                if( !$leftJoin[ $cauldronRef ] ){
                    continue;
                }

                $query  .= "LEFT JOIN `cauldron` AS `".$cauldronRef."` ";
                $query  .=  "ON ( ";

                $separator = "";
                foreach( self::RELATIONSHIPS_JOINTURE as $relationship => $jointureFunction )
                {
                    if( empty($cauldronRefConf[ $relationship ]) ){
                        continue;
                    }

                    $query .= $separator;
                    
                    $separator = "OR ";
                    $params    = [$cauldronRef, 'c'];

                    if( !empty($cauldronRefConf[ $relationship ]['depth']) ){
                        $params[] = $cauldronRefConf[ $relationship ]['depth'];
                    }

                    $query .= call_user_func_array(
                        [ __CLASS__, $jointureFunction ], 
                        array_merge( [$wc->caudronDepth], $params ) 
                    );
                }

                $query  .=  ") ";            
            }
        }
        
        $parameters = [];
        $separator = "WHERE ( ";
                
        foreach( $configuration['id'] ?? [] as $cauldronRefConf )
        {
            $cauldronRef                   = 'id_'.$cauldronRefConf['id'];
            $parameters[ $cauldronRef ]    = (int) $cauldronRefConf['id'];

            $condition  =   " %s.`id` = :".$cauldronRef." ";

            $query      .=  $separator;
            $separator  =   "OR ";
            $query      .=  str_replace(' %s.', ' `c`.', $condition);
            if( $leftJoin[ $cauldronRef ] ){
                $query      .=  " OR ".str_replace(' %s.', " `".$cauldronRef."`.", $condition);
            }
        }
        
        if( $userConnexionJointure )
        {
            $query      .=  $separator;
            $separator  =   "OR ";

            $query  .=  "( ";
            $query  .=      "( `user`.`id` = `user_connexion`.`cauldron_fk` ";
            $query  .=          "AND `user`.`data`->>\"$.structure\" = \"wc-connexion\" ) ";
            $query  .=      "OR ( `c`.`id` = `user_connexion`.`cauldron_fk` ";
            $query  .=          "AND `c`.`data`->>\"$.structure\" = \"wc-connexion\" ) ";
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

}