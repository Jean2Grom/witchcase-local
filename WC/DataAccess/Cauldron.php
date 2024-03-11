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

    const RELATIONSHIPS = [
        //'sisters',
        'parents',
        'children',
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

    static function cauldronRequest( WitchCase $wc, $configuration )
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
        if( $userConnexionJointure ){
            $query  .= ", `user_craft_table`.`id` AS `user_craft_fk` ";
        }
        
        $query  .= ", `b`.`value` AS `bool` ";
        $query  .= ", `dt`.`value` AS `datetime` ";
        $query  .= ", `f`.`value` AS `float` ";
        $query  .= ", `i`.`value` AS `int` ";
        $query  .= ", `i`.`value` AS `int` ";
        $query  .= ", `p`.`value` AS `price` ";
        $query  .= ", `t`.`value` AS `text` ";

        $query  .= ", `identifier`.`value_table` AS `ext_table` ";
        $query  .= ", `identifier`.`value_id` AS `ext_id` ";

        $query  .= "FROM ";
        if( $userConnexionJointure ){
            $query  .= "`".$wc->user->connexionData['craft_table']."` AS `user_craft_table`, ";
        }
        
        $query  .= "`cauldron` AS `c` ";

        $query  .= "LEFT JOIN `ingredient__identifier` AS `identifier` ";
        $query  .=      "ON `identifier`.`cauldron_fk` = `c`.`id` ";
        
        $query  .= "LEFT JOIN `ingredient__string` AS `s` ";
        $query  .=      "ON `s`.`cauldron_fk` = `c`.`id` ";
        
        $query  .= "LEFT JOIN `ingredient__boolean` AS `b` ";
        $query  .=      "ON `b`.`cauldron_fk` = `c`.`id` ";
        
        $query  .= "LEFT JOIN `ingredient__datetime` AS `dt` ";
        $query  .=      "ON `dt`.`cauldron_fk` = `c`.`id` ";

        $query  .= "LEFT JOIN `ingredient__float` AS `f` ";
        $query  .=      "ON `f`.`cauldron_fk` = `c`.`id` ";

        $query  .= "LEFT JOIN `ingredient__text` AS `t` ";
        $query  .=      "ON `t`.`cauldron_fk` = `c`.`id` ";

        $query  .= "LEFT JOIN `ingredient__integer` AS `i` ";
        $query  .=      "ON `i`.`cauldron_fk` = `c`.`id` ";

        $query  .= "LEFT JOIN `ingredient__price` AS `p` ";
        $query  .=      "ON `p`.`cauldron_fk` = `c`.`id` ";

        $leftJoin = [];
        foreach( $configuration as $type => $typeConfiguration ) 
        {
            if( $type === 'user' ){
                $witchRefConfJoins = [ 'user' => $typeConfiguration ];
            }
            else {
                $witchRefConfJoins = $typeConfiguration;
            }
            
            foreach( $witchRefConfJoins as $witchRef => $witchRefConf ) 
            {
                $leftJoin[ $witchRef ] = false;
                foreach( self::RELATIONSHIPS as $relationship ){
                    if( !empty($witchRefConf[ $relationship ]) )
                    {
                        $leftJoin[ $witchRef ] = true;
                        break;
                    }
                }

                if( !$leftJoin[ $witchRef ] ){
                    continue;
                }

                $query  .= "LEFT JOIN `cauldron` AS `".$witchRef."` ";
                $query  .=  "ON ( ";

                $separator = "";
                foreach( self::RELATIONSHIPS as $relationship )
                {
                    if( empty($witchRefConf[ $relationship ]) ){
                        continue;
                    }

                    $query .= $separator;
                    $separator = "OR ";

                    $functionName   = $relationship."Jointure";
                    $params         = [$witchRef, 'c'];

                    if( !empty($witchRefConf[ $relationship ]['depth']) ){
                        $params[] = $witchRefConf[ $relationship ]['depth'];
                    }

                    $query .= call_user_func_array([ __CLASS__, $functionName ], array_merge([$wc], $params) );
                }

                $query  .=  ") ";            
            }
        }
        
        $parameters = [];
        $separator = "WHERE ( ";
                
        foreach( $configuration['id'] as $witchRef => $witchRefConf )
        {
            $parameters[ $witchRef ]    = (int) $witchRefConf['id'];

            $condition  =   " %s.`id` = :".$witchRef." ";

            $query      .=  $separator;
            $separator  =   "OR ";
            $query      .=  str_replace(' %s.', ' `c`.', $condition);
            if( $leftJoin[ $witchRef ] ){
                $query      .=  " OR ".str_replace(' %s.', " `".$witchRef."`.", $condition);
            }
        }
        
        if( $userConnexionJointure )
        {
            $parameters[ 'user_craft_table' ]    = $wc->user->connexionData['craft_table'];                

            $condition  =   "( %s.`craft_table` = :user_craft_table ";
            $condition  .=  "AND %s.`craft_fk` = `user_craft_table`.`id` ) ";
            
            $query      .=  $separator;
            $separator  =   "OR ";
            $query      .=  str_replace(' %s.', ' `c`.', $condition);
            if( $leftJoin['user'] ){
                $query      .=  " OR ".str_replace(' %s.', " `user_craft_table`.", $condition);
            }            
        }
        
        $query .=  ") ";
                
        if( $wc->website->sitesRestrictions )
        {
            $sitesRestrictionsParams = [];
            foreach( $wc->website->sitesRestrictions as $sitesRestrictionsKey => $sitesRestrictionsValue )
            {
                $parameterKey                   = 'site_restriction_'.$sitesRestrictionsKey;
                $sitesRestrictionsParams[]      = $parameterKey;
                $parameters[ $parameterKey ]    = $sitesRestrictionsValue;
            }
            
            $query .=  "AND ( `c`.`site` IN ( :".implode(", :", $sitesRestrictionsParams)." ) OR `c`.`site` IS NULL ) ";
        }
        
        if( $userConnexionJointure )
        {
            $parameters[ 'user_id' ] = (int) $wc->user->id;
            $query  .= "AND `user_craft_table`.`".$wc->user->connexionData['craft_column']."` = :user_id ";
        }
        
        $userPoliciesConditions = [];
        foreach( $wc->user->policies as $policyId => $policy )
        {
            $condition = [];
            $policyKeyPrefix = ":policy_".((int) $policyId);
            /*
            if( !empty($policy['module']) && $policy['module'] != "*" )
            {
                $condition[] = "`c`.`invoke` = ".$policyKeyPrefix."_invoke ";
                $parameters[ $policyKeyPrefix.'_invoke' ] = $policy['module'];
            }
             */
            
            if( isset($policy['status']) && $policy['status'] != "*" )
            {
                $condition[] = "`c`.`status` <= ".$policyKeyPrefix."_status ";
                $parameters[ $policyKeyPrefix.'_status' ] = (int) $policy['status'];
            }
            
            if( !empty($condition) && !empty($policy['position']) )
            {
                if( $policy['position_rules']['ancestors'] xor $policy['position_rules']['descendants'] )
                {
                    $lastLevel = count($policy['position']);
                    if( $policy['position_rules']['self'] ){
                        $lastLevel++;
                    }
                }

                foreach( $policy['position'] as $level => $levelValue ){
                    if( $level <= $lastLevel )
                    {
                        $field                                      = "level_".((int) $level);
                        $condition[]                                = "`c`.`".$field."` = ".$policyKeyPrefix."_".$field." ";
                        $parameters[ $policyKeyPrefix."_".$field ]  = $levelValue;
                    }
                }
                
                if( $policy['position_rules']['ancestors'] ){
                    $condition[] = "`c`.`level_".$lastLevel."` IS NULL ";
                }
                elseif( $policy['position_rules']['descendants'] && !$policy['position_rules']['self']){
                    $condition[] = "`c`.`level_".$lastLevel."` IS NOT NULL ";
                }                
            }
            
            if( !empty($condition) ){
                $userPoliciesConditions[] = $condition;
            }
        }
        
        if( !empty($userPoliciesConditions) )
        {
            $query .= "AND ( ";
            foreach( $userPoliciesConditions as $i => $condition )
            {
                if( count($condition) == 1 ){
                    $query .= array_values($condition)[0];
                }
                else {
                    $query .= "( ".implode("AND ", $condition).") ";
                }
                
                if( ($i + 1) < count($userPoliciesConditions) ){
                    $query .= "OR "; 
                }
            }
            $query .= ") ";
        }
        
$wc->db->debugQuery($query, $parameters);

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


}