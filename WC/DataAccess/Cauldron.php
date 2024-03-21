<?php
namespace WC\DataAccess;

use WC\WitchCase;

class Cauldron
{
    const FIELDS = [
        "id",
        "content_key",
        "status",
        "name",
        "resume",
        "data",
        "priority",
        "datetime",
    ];
    
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
        foreach( self::FIELDS as $field )
        {
            $query      .=  $separator."`c`.`".$field."` ";
            $separator  =   ", ";
        }
        for( $i=1; $i<=$wc->caudronDepth; $i++ ){
            $query      .=  $separator."`c`.`level_".$i."` ";
        }
        
        $query  .= ", `b`.`value` AS `b_value` ";
        $query  .= ", `b`.`id` AS `b_id` ";
        $query  .= ", `b`.`name` AS `b_name` ";
        $query  .= ", `b`.`priority` AS `b_priority` ";

        $query  .= ", `dt`.`value` AS `dt_value` ";
        $query  .= ", `dt`.`id` AS `dt_id` ";
        $query  .= ", `dt`.`name` AS `dt_name` ";
        $query  .= ", `dt`.`priority` AS `dt_priority` ";

        $query  .= ", `f`.`value` AS `f_value` ";
        $query  .= ", `f`.`id` AS `f_id` ";
        $query  .= ", `f`.`name` AS `f_name` ";
        $query  .= ", `f`.`priority` AS `f_priority` ";

        $query  .= ", `i`.`value` AS `i_value` ";
        $query  .= ", `i`.`id` AS `i_id` ";
        $query  .= ", `i`.`name` AS `i_name` ";
        $query  .= ", `i`.`priority` AS `i_priority` ";

        $query  .= ", `p`.`value` AS `p_value` ";
        $query  .= ", `p`.`id` AS `p_id` ";
        $query  .= ", `p`.`name` AS `p_name` ";
        $query  .= ", `p`.`priority` AS `p_priority` ";

        $query  .= ", `s`.`value` AS `s_value` ";
        $query  .= ", `s`.`id` AS `s_id` ";
        $query  .= ", `s`.`name` AS `s_name` ";
        $query  .= ", `s`.`priority` AS `s_priority` ";

        $query  .= ", `t`.`value` AS `t_value` ";
        $query  .= ", `t`.`id` AS `t_id` ";
        $query  .= ", `t`.`name` AS `t_name` ";
        $query  .= ", `t`.`priority` AS `t_priority` ";

        $query  .= ", `identifier`.`value_table` AS `identifier_value_table` ";
        $query  .= ", `identifier`.`value_id` AS `identifier_value_id` ";
        $query  .= ", `identifier`.`id` AS `identifier_id` ";
        $query  .= ", `identifier`.`name` AS `identifier_name` ";
        $query  .= ", `identifier`.`priority` AS `identifier_priority` ";

        $query  .= ", `cl`.`value` AS `cl_value` ";
        $query  .= ", `cl`.`id` AS `cl_id` ";
        $query  .= ", `cl`.`name` AS `cl_name` ";
        $query  .= ", `cl`.`priority` AS `cl_priority` ";

        $query  .= "FROM ";
        if( $userConnexionJointure ){
            $query  .= "`ingredient__identifier` AS `user_connexion`, ";
        }
        
        $query  .= "`cauldron` AS `c` ";

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

                    $query .= call_user_func_array([ __CLASS__, $jointureFunction ], array_merge([$wc], $params) );
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


    private static function childrenJointure( WitchCase $wc, $mother, $daughter, $depth=1 )
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
        
        for( $i=2; $i <= $wc->depth; $i++ )
        {
            $jointure  .=  "AND ( ";
            $jointure  .=      "( ".$m($i)." IS NOT NULL AND ".$d($i)." = ".$m($i)." ) ";
            $jointure  .=      "OR ( ".$m($i)." IS NULL AND ".$m($i-1)." IS NOT NULL AND ".$d($i)." IS NOT NULL ) ";
            $jointure  .=      "OR (  ".$m($i)." IS NULL AND ".$m($i-1)." IS NULL ";
            // Apply level
            if( $depth != '*' && ($depth + $i - 1) <= $wc->depth ){
                $jointure  .=       "AND ".$d($depth + $i - 1)." IS NULL ";
            }
            $jointure  .=      ") ";
            $jointure  .=  ") ";
        }
        
        return $jointure;
    }
    
    private static function parentsJointure( WitchCase $wc, $daughter, $mother, $depth=1 )
    {
        return self::childrenJointure( $wc, $mother, $daughter, $depth );
    }


    private static function siblingsJointure( WitchCase $wc, $witch, $sister, $depth=1 )
    {
        $w = function (int $level) use ($witch): string {
            return "`".$witch."`.`level_".$level."`";
        };
        $s = function (int $level) use  ($sister): string {
            return "`".$sister."`.`level_".$level."`";
        };
        
        $jointure = "( `".$witch."`.`id` <> `".$sister."`.`id` ) ";
        
        for( $i=1; $i < $wc->depth; $i++ )
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
        
        $maxDepth = (int) $wc->depth;
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