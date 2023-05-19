<?php
namespace WC\DataAccess;

use WC\WitchCase;
use WC\Witch;

/**
 * Description of WitchSummoning
 *
 * @author teletravail
 */
class WitchSummoning 
{
    const RELATIONSHIPS = [
        'sisters',
        'parents',
        'children',
    ];
           
    static function getDepth( WitchCase $wc ): int
    {
        $depth = $wc->cache->read( 'system', 'depth' );
        
        if( empty($depth) )
        {
            $query  =   "SHOW COLUMNS FROM `witch` WHERE `Field` LIKE 'level_%'";
            $result =   $wc->db->selectQuery($query);
            $depth  =   count($result);
            
            $wc->cache->create('system', 'depth', $depth);
        }
        
        return (int) $depth;
    }
    
    
    private static function witchesInstanciate( WitchCase $wc, $configuration, $result )
    {
        $witches        = [];
        $witchesList    = [];
        
        $depthArray = [];
        foreach( range(0, $wc->depth) as $d ){
            $depthArray[ $d ] = [];
        }
        
        $conditions     = [];
        $urlRefWiches   = [];
        foreach( $configuration as $witchRef => $witchRefConf )
        {
            if( !empty($witchRefConf['url']) )
            {
                $conditions[ $witchRef ] = [ 
                    'site'  => $witchRefConf['website_name'],
                    'url'   => $witchRefConf['website_url'],
                ];
                
                $urlRefWiches[] = $witchRef;
            }
            elseif( !empty($witchRefConf['id']) ){
                $conditions[ $witchRef ] = [ 
                    'id'    => $witchRefConf['id'], 
                ];
            }
            elseif( !empty($witchRefConf['user']) && !empty($result[0]['user_craft_fk']) ){
                $conditions[ $witchRef ] = [ 
                    'craft_table'  => $wc->user->connexionData['craft_table'], 
                    'craft_fk'     => $result[0]['user_craft_fk'], 
                ];
            }
        }
        
        foreach( $result as $row )
        {
            $id                             = $row['id'];
            $witch                          = Witch::createFromData( $wc, $row );
            $depthArray[ $witch->depth ][]  = $id;
            $witchesList[ $id ]             = $witch;
            
            foreach( $conditions as $witchRef => $conditionsItem )
            {
                $matched = true;
                foreach( $conditionsItem as $field => $value ){
                    if( $row[ $field ] != $value )
                    {
                        $matched = false;
                        break;
                    }
                }
                
                if( $matched ){
                    $witches[ $witchRef ] = $witch;
                }
            }
        }
        
        for( $i=0; $i < $wc->depth; $i++ ){
            foreach( $depthArray[ $i ] as $potentialMotherId ){
                foreach( $depthArray[ ($i+1) ] as $potentialDaughterId ){
                    if( $witchesList[ $potentialMotherId ]->isMotherOf( $witchesList[ $potentialDaughterId ] ) ){
                        $witchesList[ $potentialMotherId ]->addDaughter( $witchesList[ $potentialDaughterId ] );
                    }
                }
            }
        }
        
        foreach( $urlRefWiches as $urlRefWichItem ){
            if( empty($witches[ $urlRefWichItem ]) ){
                $witches[ $urlRefWichItem ] = Witch::createFromData( $wc, [ 'name' => "ABSTRACT 404 WITCH", 'invoke' => '404' ] );
            }
        }
        
        return $witches;
    }
    
    private static function witchesRequest( WitchCase $wc, $configuration, $userConnexionJointure=false )
    {
        // Determine the list of fields in select part of query
        $query = "";
        $separator = "SELECT DISTINCT ";
        foreach( Witch::FIELDS as $field )
        {
            $query      .=  $separator."`w`.`".$field."` ";
            $separator  =   ", ";
        }
        for( $i=1; $i<=$wc->depth; $i++ ){
            $query      .=  $separator."`w`.`level_".$i."` ";
        }
        if( $userConnexionJointure ){
            $query  .= ", `user_craft_table`.`id` AS `user_craft_fk` ";
        }
        
        $query  .= "FROM ";
        if( $userConnexionJointure ){
            $query  .= "`".$wc->user->connexionData['craft_table']."` AS `user_craft_table`, ";
        }
        
        $refWitch = false;
        foreach( $configuration as $witchRef => $witchRefConf ){
            if( !empty($witchRefConf['module']) )
            {
                $query  .= "`witch` AS `ref_witch`, ";
                $refWitch = true;
                break;
            }
        }
        
        $query  .= "`witch` AS `w` ";
        
        $leftJoin = [];
        foreach( $configuration as $witchRef => $witchRefConf ) 
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
                
                $query .= call_user_func_array([ __CLASS__, $functionName.'XXX' ], array_merge([$wc], $params) );
            }
            
            $query  .=  ") ";            
        }
        
        $parameters = [];
        $separator = "WHERE ( ";
        foreach( $configuration as $witchRef => $witchRefConf )
        {
            if( !empty($witchRefConf['url']) )
            {
                $parameters['website_name']   = $witchRefConf['website_name'];
                $parameters['website_url']    = $witchRefConf['website_url'];
                
                $condition  =   "( %s.`site` = :website_name ";
                $condition  .=  "AND %s.`url` = :website_url ) ";
            }
            elseif( !empty($witchRefConf['id']) )
            {
                $parameterKey                   = $witchRef.'_id';
                $parameters[ $parameterKey ]    = (int) $witchRefConf['id'];
                
                $condition  =   " %s.`id` = :".$parameterKey." ";
                if( !empty($witchRefConf['module']) )
                {
                    $parameterKey                   = $witchRef.'_module';
                    $parameters[ $parameterKey ]    = $witchRefConf['module'];
                    
                    $condition  =   "( ".$condition." AND `ref_witch`.`invoke` = :".$parameterKey." ) ";
                }
            }
            elseif( !empty($witchRefConf['user']) )
            {
                $parameterKey                   = $witchRef.'_user_table';
                $parameters[ $parameterKey ]    = $wc->user->connexionData['craft_table'];                
                
                $condition  =   "( %s.`craft_table` = :".$parameterKey." ";
                $condition  .=  "AND %s.`craft_fk` = `user_craft_table`.`id` ) ";
            }
            
            $query      .=  $separator;
            $separator  =   "OR ";
            $query      .=  str_replace(' %s.', ' `w`.', $condition);
            if( $leftJoin[ $witchRef ] ){
                $query      .=  " OR ".str_replace(' %s.', " `".$witchRef."`.", $condition);
            }
        }
        $query .=  ") ";
        
        if( $refWitch )
        {
            $parameters[ 'website_name' ]   = $parameters['website_name'] ?? $wc->website->name;
            $parameters[ 'website_url' ]    = $parameters['website_url'] ?? $wc->website->urlPath;
            
            $query .=  "AND ( `ref_witch`.`site` = :website_name ";
            $query .=  "AND `ref_witch`.`url` = :website_url ) ";
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
        
        return $wc->db->selectQuery($query, $parameters);
    }

    private static function childrenJointureXXX( WitchCase $wc, $mother, $daughter, $depth=1 )
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
    
    private static function parentsJointureXXX( WitchCase $wc, $daughter, $mother, $depth=1 )
    {
        return self::childrenJointureXXX( $wc, $mother, $daughter, $depth );
    }
    
    private static function sistersJointureXXX( WitchCase $wc, $witch, $sister, $depth=1 )
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
    
    
    
    static function summonXXX( WitchCase $wc, $configuration )
    {
        $userConnexionJointure = false;
        foreach( $configuration as $refWitchName => $refWitchSummoning ){
            if( !empty($refWitchSummoning['user']) && !$wc->user->connexion ){
                unset($configuration[ $refWitchName ]);
            }
            elseif( !empty($refWitchSummoning['user']) ){
                $userConnexionJointure = true;
            }
        }
        
        if( empty($configuration) ){
            return [];
        }
        
        //$result     = $this->initialWitchesRequest( $userConnexionJointure );
        $result     = self::witchesRequest($wc, $configuration, $userConnexionJointure);
        
        if( $result === false ){
            return false;
        }
        
        //return $this->initialWitchesInstanciate( $result );
        return self::witchesInstanciate($wc, $configuration, $result);
    }
    
    
}
