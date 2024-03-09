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

    static function witchesRequest( WitchCase $wc, $configuration )
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
        for( $i=1; $i<=$wc->depth; $i++ ){
            $query      .=  $separator."`c`.`level_".$i."` ";
        }
        if( $userConnexionJointure ){
            $query  .= ", `user_craft_table`.`id` AS `user_craft_fk` ";
        }
        
        $query  .= "FROM ";
        if( $userConnexionJointure ){
            $query  .= "`".$wc->user->connexionData['craft_table']."` AS `user_craft_table`, ";
        }
        
        $refWitch           = false;
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
                if( !empty($witchRefConf['modules']) ){
                    $query  .= "`witch` AS `ref_witch`, ";
                    $refWitch = true;
                    break;
                }

            }
        }
        
        $query  .= "`witch` AS `w` ";
        
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

                $query  .= "LEFT JOIN `witch` AS `".$witchRef."` ";
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
                    $params         = [$witchRef, 'w'];

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
        
        foreach( $configuration['url'] as $witchRef => $witchRefConf )
        {
            $parameters[$witchRef.'_site']  = $witchRefConf['site'];
            $parameters[$witchRef.'_url']   = $witchRefConf['url'];

            $condition  =   "( %s.`site` = :".$witchRef."_site ";
            $condition  .=  "AND %s.`url` = :".$witchRef."_url ) ";

            $query      .=  $separator;
            $separator  =   "OR ";
            $query      .=  str_replace(' %s.', ' `w`.', $condition);
            if( $leftJoin[ $witchRef ] ){
                $query      .=  " OR ".str_replace(' %s.', " `".$witchRef."`.", $condition);
            }
        }
        
        foreach( $configuration['id'] as $witchRef => $witchRefConf )
        {
            $parameters[ $witchRef ]    = (int) $witchRefConf['id'];

            $condition  =   " %s.`id` = :".$witchRef." ";
              
            if( !empty($witchRefConf['modules']) && $refWitch )
            {
                $innerCondition = [];
                foreach( $witchRefConf['modules'] as $module )
                {
                    $parameterKey                   = $witchRef.'_'.$module;
                    $parameters[ $parameterKey ]    = $module;
                    $innerCondition[]               = "`ref_witch`.`invoke` = :".$parameterKey." ";
                }

                $condition  =   "( ".$condition." AND ( ". join("OR ", $innerCondition)." ) ) ";
            }
            
            $query      .=  $separator;
            $separator  =   "OR ";
            $query      .=  str_replace(' %s.', ' `w`.', $condition);
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
            $query      .=  str_replace(' %s.', ' `w`.', $condition);
            if( $leftJoin['user'] ){
                $query      .=  " OR ".str_replace(' %s.', " `user_craft_table`.", $condition);
            }            
        }
        
        $query .=  ") ";
        
        if( $refWitch )
        {
            if( empty($parameters['site']) || empty($parameters['url']) ){
                $parameters = array_replace($parameters, $wc->website->getUrlSearchParameters());
            }
            
            $query .=  "AND ( `ref_witch`.`site` = :site ";
            $query .=  "AND `ref_witch`.`url` = :url ) ";
        }
        
        if( $wc->website->sitesRestrictions )
        {
            $sitesRestrictionsParams = [];
            foreach( $wc->website->sitesRestrictions as $sitesRestrictionsKey => $sitesRestrictionsValue )
            {
                $parameterKey                   = 'site_restriction_'.$sitesRestrictionsKey;
                $sitesRestrictionsParams[]      = $parameterKey;
                $parameters[ $parameterKey ]    = $sitesRestrictionsValue;
            }
            
            $query .=  "AND ( `w`.`site` IN ( :".implode(", :", $sitesRestrictionsParams)." ) OR `w`.`site` IS NULL ) ";
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
                $condition[] = "`w`.`invoke` = ".$policyKeyPrefix."_invoke ";
                $parameters[ $policyKeyPrefix.'_invoke' ] = $policy['module'];
            }
             */
            
            if( isset($policy['status']) && $policy['status'] != "*" )
            {
                $condition[] = "`w`.`status` <= ".$policyKeyPrefix."_status ";
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
                        $condition[]                                = "`w`.`".$field."` = ".$policyKeyPrefix."_".$field." ";
                        $parameters[ $policyKeyPrefix."_".$field ]  = $levelValue;
                    }
                }
                
                if( $policy['position_rules']['ancestors'] ){
                    $condition[] = "`w`.`level_".$lastLevel."` IS NULL ";
                }
                elseif( $policy['position_rules']['descendants'] && !$policy['position_rules']['self']){
                    $condition[] = "`w`.`level_".$lastLevel."` IS NOT NULL ";
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
        
        return $wc->db->selectQuery($query, $parameters);
    }


}