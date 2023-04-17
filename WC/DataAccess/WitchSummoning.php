<?php
namespace WC\DataAccess;

use WC\WitchCase;
use WC\Website;
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
    
    var $configuration;
    var $getParams;
    var $website;
    var $sitesRestrictions;
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc, array $summoningConfiguration, Website $website )
    {
        $this->wc                   = $wc;
        $this->configuration        = $summoningConfiguration;
        $this->getParams            = [];
        $this->website              = $website;
        $this->sitesRestrictions    = $this->website->sitesRestrictions;
        foreach( $this->configuration as $refWitchName => $refWitchSummoning )
        {
            $unset = false;
            if( empty($refWitchSummoning) ){
                $unset = true;
            }
            
            if( !empty($refWitchSummoning['get']) )
            {
                $paramValue = $this->wc->request->param($refWitchSummoning['get'], 'get');
                if( $paramValue ){
                    $this->getParams[ $refWitchSummoning['get'] ] = $paramValue;
                }
                else {
                    $unset = true;
                }
            }
            
            if( $unset )
            {
                unset($this->configuration[ $refWitchName ]);
                continue;
            }
            
            foreach( $refWitchSummoning as $refWitchSummoningParam => $refWitchSummoningValue ){
                if( is_array($refWitchSummoningValue) ){
                    foreach( $refWitchSummoningValue as $refWitchSummoningValueKey => $refWitchSummoningValueItem ){
                        if( is_numeric($refWitchSummoningValueItem) ){
                            $this->configuration[ $refWitchName ][ $refWitchSummoningParam ][ $refWitchSummoningValueKey ] = (integer) $refWitchSummoningValueItem;
                        }
                    }
                }
            }
        }
        
        foreach( $this->configuration as $refWitchName => $refWitchSummoning ){
            if( !empty($refWitchSummoning['sisters']) && empty($refWitchSummoning['parents']) ){
                $this->configuration[ $refWitchName ]['parents'] = [
                    "depth" => 1,
                    "craft" => false
                ];
            }
        }
    }
    
    function summon()
    {
        $userConnexionJointure = false;
        foreach( $this->configuration as $refWitchName => $refWitchSummoning ){
            if( !empty($refWitchSummoning['user']) && !$this->wc->user->connexion ){
                unset($this->configuration[ $refWitchName ]);
            }
            elseif( !empty($refWitchSummoning['user']) ){
                $userConnexionJointure = true;
            }
        }
        
        if( empty($this->configuration) ){
            return [];
        }
        
        $result     = $this->initialWitchesRequest( $userConnexionJointure );
        
        if( $result === false ){
            return false;
        }
        
        return $this->initialWitchesInstanciate( $result );
    }
    
    private function initialWitchesRequest( $userConnexionJointure=false )
    {
        // Determine the list of fields in select part of query
        $query = "";
        $separator = "SELECT DISTINCT ";
        foreach( Witch::FIELDS as $field )
        {
            $query      .=  $separator."`w`.`".$field."` ";
            $separator  =   ", ";
        }
        for( $i=1; $i<=$this->website->depth; $i++ ){
            $query      .=  $separator."`w`.`level_".$i."` ";
        }
        if( $userConnexionJointure ){
            $query  .= ", `user_target_table`.`id` AS `user_target_fk` ";
        }
        
        $query  .= "FROM ";
        if( $userConnexionJointure ){
            $query  .= "`".$this->wc->user->connexionData["target_table"]."` AS `user_target_table`, ";
        }
        
        $refWitch = false;
        foreach( $this->configuration as $witchRef => $witchRefConf ){
            if( !empty($witchRefConf['module']) )
            {
                $query  .= "`witch` AS `ref_witch`, ";
                $refWitch = true;
                break;
            }
        }
        
        $query  .= "`witch` AS `w` ";
        
        $leftJoin = [];
        foreach( $this->configuration as $witchRef => $witchRefConf ) 
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
                
                $query .= call_user_func_array([ $this, $functionName ], $params);
            }
            
            $query  .=  ") ";            
        }
        
        $parameters = [];
        $separator = "WHERE ( ";
        foreach( $this->configuration as $witchRef => $witchRefConf )
        {
            if( !empty($witchRefConf['url']) )
            {
                $parameters[ 'website_name' ]   = $this->website->name;
                $parameters[ 'website_url' ]    = $this->website->urlPath;
                
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
            elseif( !empty($witchRefConf['get']) )
            {
                $parameterKey                   = $witchRef.'_get';
                $parameters[ $parameterKey ]    = $this->getParams[ $witchRefConf['get'] ];
                
                $condition  =   " %s.`id` = :".$parameterKey." ";
            }
            elseif( !empty($witchRefConf['user']) )
            {
                $parameterKey                   = $witchRef.'_user_table';
                $parameters[ $parameterKey ]    = $this->wc->user->connexionData["target_table"];                
                
                $condition  =   "( %s.`target_table` = :".$parameterKey." ";
                $condition  .=  "AND %s.`target_fk` = `user_target_table`.`id` ) ";
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
            $parameters[ 'website_name' ]   = $this->website->name;
            $parameters[ 'website_url' ]    = $this->website->urlPath;
            
            $query .=  "AND ( `ref_witch`.`site` = :website_name ";
            $query .=  "AND `ref_witch`.`url` = :website_url ) ";
        }
        
        if( $this->sitesRestrictions )
        {
            $sitesRestrictionsParams = [];
            foreach( $this->sitesRestrictions as $sitesRestrictionsKey => $sitesRestrictionsValue )
            {
                $parameterKey                   = 'site_restriction_'.$sitesRestrictionsKey;
                $sitesRestrictionsParams[]      = $parameterKey;
                $parameters[ $parameterKey ]    = $sitesRestrictionsValue;
            }
            
            $query .=  "AND ( `w`.`site` IN ( :".implode(", :", $sitesRestrictionsParams)." ) OR `w`.`site` IS NULL ) ";
        }
        
        if( $userConnexionJointure )
        {
            $parameters[ 'user_id' ] = (int) $this->wc->user->id;
            $query  .= "AND `user_target_table`.`".$this->wc->user->connexionData["target_column"]."` = :user_id ";
        }
        
        return $this->wc->db->selectQuery($query, $parameters);
    }
    
    private function childrenJointure( $mother, $daughter, $depth=1 )
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
        
        for( $i=2; $i<=$this->website->depth; $i++ )
        {
            $jointure  .=  "AND ( ";
            $jointure  .=      "( ".$m($i)." IS NOT NULL AND ".$d($i)." = ".$m($i)." ) ";
            $jointure  .=      "OR ( ".$m($i)." IS NULL AND ".$m($i-1)." IS NOT NULL AND ".$d($i)." IS NOT NULL ) ";
            $jointure  .=      "OR (  ".$m($i)." IS NULL AND ".$m($i-1)." IS NULL ";
            // Apply level
            if( $depth != '*' && ($depth + $i - 1) <= $this->website->depth ){
                $jointure  .=       "AND ".$d($depth + $i - 1)." IS NULL ";
            }
            $jointure  .=      ") ";
            $jointure  .=  ") ";
        }
        
        return $jointure;
    }
    
    private function parentsJointure( $daughter, $mother, $depth=1 )
    {
        return $this->childrenJointure( $mother, $daughter, $depth );
    }
    
    private function sistersJointure( $witch, $sister, $depth=1 )
    {
        $w = function (int $level) use ($witch): string {
            return "`".$witch."`.`level_".$level."`";
        };
        $s = function (int $level) use  ($sister): string {
            return "`".$sister."`.`level_".$level."`";
        };
        
        $jointure = "( `".$witch."`.`id` <> `".$sister."`.`id` ) ";
        
        for( $i=1; $i<$this->website->depth; $i++ )
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
        
        $maxDepth = (int) $this->website->depth;
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
    
    private function initialWitchesInstanciate( $result )
    {
        $witches        = [];
        $witchesList    = [];
        
        $depthArray = [];
        foreach( range(0, $this->website->depth) as $d ){
            $depthArray[ $d ] = [];
        }
        
        $conditions     = [];
        $urlRefWiches   = [];
        foreach( $this->configuration as $witchRef => $witchRefConf )
        {
            if( !empty($witchRefConf['url']) )
            {
                $conditions[ $witchRef ] = [ 
                    'site'  => $this->website->name, 
                    'url'   => $this->website->urlPath, 
                ];
                
                $urlRefWiches[] = $witchRef;
            }
            elseif( !empty($witchRefConf['id']) ){
                $conditions[ $witchRef ] = [ 
                    'id'    => $witchRefConf['id'], 
                ];
            }
            elseif( !empty($witchRefConf['get']) ){
                $conditions[ $witchRef ] = [ 
                    'id'    => $this->getParams[ $witchRefConf['get'] ], 
                ];
            }
            elseif( !empty($witchRefConf['user']) && !empty($result[0]['user_target_fk']) ){
                $conditions[ $witchRef ] = [ 
                    'target_table'  => $this->wc->user->connexionData["target_table"], 
                    'target_fk'     => $result[0]['user_target_fk'], 
                ];
            }
        }
        
        foreach( $result as $row )
        {
            $id                             = $row['id'];
            $witch                          = Witch::createFromData( $this->wc, $row );
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
        
        for( $i=0; $i < $this->website->depth; $i++ ){
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
                $witches[ $urlRefWichItem ] = Witch::createFromData( $this->wc, [ 'name' => "ABSTRACT 404 WITCH", 'invoke' => '404' ] );
            }
        }
        
        return $witches;
    }
    
    
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
}
