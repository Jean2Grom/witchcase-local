<?php
namespace WC\DataAccess;

use WC\WitchCase;
use WC\Website;
use WC\Module;
use WC\Target;
use WC\TargetStructure;
use WC\Attribute;

/**
 * Description of WitchCrafting
 *
 * @author teletravail
 */
class WitchCrafting 
{    
    var $configuration;
    var $website;
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc, array $summoningConfiguration, Website $website=NULL )
    {
        $this->wc                   = $wc;
        $this->configuration        = $summoningConfiguration;
        $this->website              = $website ?? $this->wc->website;
    }

    function craft( $witches )
    {
        $targetsToCraft = [];
        foreach( $this->configuration as $refWitch => $witchConf ){
            if( !empty($witches[ $refWitch ]) )
            {
                if( !empty($witches[ $refWitch ]->invoke) )
                {
                    if( !empty($witchConf['invoke']) && is_string($witchConf['invoke']) ){
                        $permission = $witches[ $refWitch ]
                                            ->isAllowed( 
                                                (new Module( $witches[ $refWitch ], $witchConf['invoke'] )) 
                                            );
                    }
                    else {
                        $permission = $witches[ $refWitch ]->isAllowed();
                    }
                    
                    if( !$permission ){
                        continue;
                    }
                }
                
                if( !isset($witchConf['craft']) || !empty($witchConf['craft']) )
                {
                    $table  = $witches[ $refWitch ]->target_table;
                    $fk     = (int) $witches[ $refWitch ]->target_fk;
                    
                    if( !empty($table) && !empty($fk) )
                    {
                        if( empty($targetsToCraft[ $table ]) ){
                            $targetsToCraft[ $table ] = [];
                        }
                        
                        if( !in_array($fk, $targetsToCraft[ $table ]) ){
                            $targetsToCraft[ $table ][] = $fk;
                        }
                    }
                }
                
                if( !empty($witchConf['parents']['craft']) ){
                    $targetsToCraft = array_merge_recursive( 
                        $targetsToCraft, 
                        $this->getParentsCraftData( $witches[ $refWitch ], $witchConf['parents']['craft'] )
                    );

                }
                
                if( !empty($witchConf['sisters']['craft']) && !empty($witches[ $refWitch ]->sisters) ){
                    foreach( $witches[ $refWitch ]->sisters as $sisterId => $sisterWitch ){
                        $targetsToCraft = array_merge_recursive( 
                            $targetsToCraft, 
                            $this->getChildrenCraftData( $sisterWitch, $witchConf['sisters']['craft'] )
                        );
                    }
                }
                
                if( !empty($witchConf['children']['craft']) ){
                    $targetsToCraft = array_merge_recursive( 
                        $targetsToCraft, 
                        $this->getChildrenCraftData( $witches[ $refWitch ], $witchConf['children']['craft'] )
                    );
                }
            }
        }
                
        $craftedData     = [];
        foreach( $targetsToCraft as $table => $ids )
        {
            $craftedData[ $table ]  = [];
            $idList                 = [];
            
            $cachedData = $this->wc->cache->read( 'craft', $table ) ?? [];
            
            foreach( array_unique($ids) as $id ){
                if( isset( $cachedData[ $id ]) ){
                    $craftedData[ $table ][ $id ] = $cachedData[ $id ];
                }
                else {
                    $idList[] = $id;
                }
            }
            
            if( !empty($idList) )
            {
                $craftedData[ $table ]  = array_replace($craftedData[ $table ], $this->craftQuery( $table, $idList ));
                $this->wc->cache->create( 'craft', $table, array_replace($cachedData, $craftedData[ $table ]) );
            }
            $cachedData = null;
        }
        
        return $craftedData;
    }

    // RECURSIVE READ CRAFT DATA FUNCTIONS
    private function getChildrenCraftData( $witch, $craftLevel )
    {
        $targetsToCraft = [];
        if( !empty($witch->daughters) ){
            foreach( $witch->daughters as $daughterWitch )
            {
                $table  = $daughterWitch->target_table;
                $fk     = (int) $daughterWitch->target_fk;
                
                if( !empty($table) && !empty($fk) )
                {
                    if( empty($targetsToCraft[ $table ]) ){
                        $targetsToCraft[ $table ] = [];
                    }
                    
                    if( !in_array($fk, $targetsToCraft[ $table ]) ){
                        $targetsToCraft[ $table ][] = $fk;
                    }
                }
                
                if( $craftLevel == "*" ){
                    $craftSubLevel = $craftLevel;
                }
                else 
                {
                    $craftSubLevel = $craftLevel - 1;
                    if( $craftSubLevel == 0 ){
                        continue;
                    }
                }
                
                $targetsToCraft = array_merge_recursive(
                    $targetsToCraft, 
                    $this->getChildrenCraftData($daughterWitch, $craftSubLevel) 
                );
            }
        }
        
        return $targetsToCraft;
    }
    
    private function getParentsCraftData( $witch, $craftLevel )
    {
        $targetsToCraft = [];
        if( !empty($witch->mother) )
        {
            $motherWitch    = $witch->mother;
            
            $table          = $motherWitch->target_table;
            $fk             = (int) $motherWitch->target_fk;
            
            if( !empty($table) && !empty($fk) )
            {
                if( empty($targetsToCraft[ $table ]) ){
                    $targetsToCraft[ $table ] = [];
                }

                if( !in_array($fk, $targetsToCraft[ $table ]) ){
                    $targetsToCraft[ $table ][] = $fk;
                }
            }

            if( $craftLevel == "*" ){
                $craftSubLevel = $craftLevel;
            }
            else {
                $craftSubLevel = $craftLevel - 1;
            }

            if( $craftSubLevel == "*" || $craftSubLevel > 0 ){
                $targetsToCraft = array_merge_recursive(
                    $targetsToCraft, 
                    $this->getParentsCraftData($motherWitch, $craftSubLevel) 
                );
            }
        }
        
        return $targetsToCraft;
    }
    
    
    function craftQuery( string $table, array $ids )
    {
        if( empty($table) || empty($ids) ){
            return [];
        }
        
        $structure = new TargetStructure( $this->wc, $table );
        
        $querySelectElements    = [];
        $queryTablesElements    = [];
        $queryWhereElements     = [];
        $queryParameters        = [];
        
        foreach( $ids as $paramKey => $paramValue ){
            $queryParameters[ $table.'_'.$paramKey ] = $paramValue;
        }

        $queryWhereElements[]   = "`".$structure->table."`.`id` IN ( :".implode(', :', array_keys($queryParameters)).") ";

        foreach( array_keys(Target::ELEMENTS) as $commonStructureField )
        {
            $field  =   "`".$structure->table."`.`".$commonStructureField."` ";
            $field  .=  "AS `".$structure->table."|".$commonStructureField."` ";
            $querySelectElements[] = $field;
        }

        foreach( $structure->attributes as $attributeName => $attributeData )
        {
            $attribute = new $attributeData['class']( $this->wc, $attributeName );

            array_push( $querySelectElements, ...$attribute->getSelectFields($structure->table) );
            array_push( $queryTablesElements, ...$attribute->getJointure($structure->table) );
        }
        
        $query = "";
        $query  .=  "SELECT ".implode( ', ', $querySelectElements)." ";
        $query  .=  "FROM "." `".$table."` ";

        foreach( $queryTablesElements as $leftJoin ){
            $query  .=  $leftJoin." ";
        }

        $query  .=  "WHERE ".implode( 'AND ', $queryWhereElements )." ";
        
        $result = $this->wc->db->selectQuery( $query, $queryParameters );
        
        $craftedData = [];
        foreach( $result as $row ){
            foreach( $row as $rowField => $rowFieldValue )
            {
                $splitSelectField = Attribute::splitSelectField( $rowField );
                
                $table          = $splitSelectField['table'];
                $field          = $splitSelectField['field'];
                $fieldElement   = $splitSelectField['element'];
                
                $currentId      = $row[ $table.'|id' ];

                if( empty($craftedData[ $table ]) ){
                    $craftedData[ $table ] = [];
                }
                if( empty($craftedData[ $table ][ $currentId ]) ){
                    $craftedData[ $table ][ $currentId ] = [];
                }
                if( empty($craftedData[ $table ][ $currentId ][ $field ]) ){
                    $craftedData[ $table ][ $currentId ][ $field ] = [];
                }
                
                if( empty($fieldElement) ){
                    $craftedData[ $table ][ $currentId ][ $field ] = $rowFieldValue;
                }
                elseif( empty($craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ]) ){
                    $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ] = $rowFieldValue;
                }
                elseif( !is_array($craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ]) )
                {
                    $prevValue = $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ];

                    if( $prevValue != $rowFieldValue ){
                        $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ] = [
                            $prevValue,
                            $rowFieldValue,
                        ];
                    }
                }
                else {
                    $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ][] = $rowFieldValue;
                }
            }
        }
        
        return $craftedData[ $table ];
    }
    
}
