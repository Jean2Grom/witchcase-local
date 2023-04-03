<?php
namespace WC\DataAccess;

use WC\WitchCase;
use WC\Website;
use WC\Module;
use WC\Target;
use WC\TargetStructure;

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
        
        foreach( $targetsToCraft as $table => $ids ){
            $targetsToCraft[ $table ]= array_unique($ids);
        }
        
        $structures = [];
        foreach( array_keys($targetsToCraft) as $table ){
            $structures[ $table ] = new TargetStructure( $this->wc, $table );
        }
        
        $querySelectElements    = [];
        $queryTablesElements    = [];
        $queryWhereElements     = [];
        $queryParameters        = [];
        foreach( $structures as $structureKey => $targetStructure )
        {
            $queryTablesElements[ $targetStructure->table ] = [];
            
            foreach( $targetsToCraft[ $structureKey ] as $paramKey => $paramValue ){
                $queryParameters[ $structureKey.'_'.$paramKey ] = $paramValue;
            }
            
            $queryWhereElements[]   = "`".$targetStructure->table."`.`id` IN ( :".implode(', :', array_keys($queryParameters)).") ";
            
            foreach( array_keys(Target::ELEMENTS) as $commonStructureField )
            {
                $field  =   "`".$targetStructure->table."`.`".$commonStructureField."` ";
                $field  .=  "AS `".$targetStructure->table."|".$commonStructureField."` ";
                $querySelectElements[] = $field;
            }
            
            foreach( $targetStructure->attributes as $attributeName => $attributeData )
            {
                $attribute = new $attributeData['class']( $this->wc, $attributeName );
                
                array_push( $querySelectElements, ...$attribute->getSelectFields($targetStructure->table) );
                array_push( $queryTablesElements[ $targetStructure->table ], ...$attribute->getJointure($targetStructure->table) );
            }
        }
        
        $result = [];
        if( !empty($targetsToCraft) )
        {
            $query = "";
            $query  .=  "SELECT ".implode( ', ', $querySelectElements)." ";
            $separator = "FROM ";
            foreach( $queryTablesElements as $fromTable => $leftJoinArray )
            {
                $query  .=  $separator." `".$fromTable."` ";
                $separator = ", ";
                
                foreach( $leftJoinArray as $leftJoin ){
                    $query  .=  $leftJoin." ";
                }
            }
            
            $query  .=  "WHERE ".implode( 'AND ', $queryWhereElements )." ";
            
            $result = $this->wc->db->selectQuery($query, $queryParameters);
        }
        
        $craftedData = [];
        foreach( $result as $row ){
            foreach( $row as $rowField => $rowFieldValue )
            {
                $buffer         = explode('|', $rowField);
                $table          = $buffer[0];
                $subBuffer      = explode('#', $buffer[1]);
                $field          = $subBuffer[0];
                $fieldElement   = $subBuffer[1] ?? false;
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
                //elseif( !in_array($rowFieldValue, $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ]) ){
                else {
                    $craftedData[ $table ][ $currentId ][ $field ][ $fieldElement ][] = $rowFieldValue;
                }
            }
        }
        
        // SETTINGS CRAFTS (Targets)
        foreach( $this->configuration as $refWitch => $witchConf ){
            if( !empty($witches[ $refWitch ]) )
            {
                if( !isset($witchConf['craft']) || !empty($witchConf['craft']) )
                {
                    $data       = $craftedData[ $witches[ $refWitch ]->target_table ][ $witches[ $refWitch ]->target_fk ] ?? null;
                    $structure  = $structures[ $witches[ $refWitch ]->target_table ] ?? null;
                    
                    if( !empty($data) && !empty($structure) ){
                        $witches[ $refWitch ]->craft( $data, $structure );
                    }
                }
                
                if( !empty($witchConf['parents']['craft']) ){
                    $this->setParentsCraftData( 
                        $witches[ $refWitch ], 
                        $witchConf['parents']['craft'], 
                        $craftedData, 
                        $structures 
                    );
                }
                
                if( !empty($witchConf['sisters']['craft']) && !empty($witches[ $refWitch ]->sisters) ){
                    foreach( $witches[ $refWitch ]->sisters as $sisterId => $sisterWitch ){
                        $this->setChildrenCraftData( 
                            $sisterWitch, 
                            $witchConf['sisters']['craft'], 
                            $craftedData, 
                            $structures 
                        );
                    }
                }
                
                if( !empty($witchConf['children']['craft']) ){
                    $this->setChildrenCraftData( 
                        $witches[ $refWitch ], 
                        $witchConf['children']['craft'], 
                        $craftedData, 
                        $structures 
                    );
                }
            }
        }

        return $witches;
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
    

    // RECURSIVE CRAFT FUNCTIONS
    private function setChildrenCraftData( $witch, $craftLevel, $craftedData, $structures )
    {
        $targetsToCraft = [];
        if( !empty($witch->daughters) ){
            foreach( $witch->daughters as $daughterWitch )
            {
                $table  = $daughterWitch->target_table;
                $fk     = (int) $daughterWitch->target_fk;
                
                if( !empty($table) && !empty($fk) 
                        && !empty($craftedData[ $table ][ $fk ])
                        && !empty($structures[ $table ])
                ){
                    $daughterWitch->craft( 
                        $craftedData[ $table ][ $fk ],
                        $structures[ $table ]
                    );
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
                
                $this->setChildrenCraftData( $daughterWitch, $craftSubLevel, $craftedData, $structures );
            }
        }
        
        return $targetsToCraft;
    }
    
    private function setParentsCraftData( $witch, $craftLevel, $craftedData, $structures )
    {
        if( !empty($witch->mother) )
        {
            $motherWitch    = $witch->mother;
            
            $table          = $motherWitch->target_table;
            $fk             = (int) $motherWitch->target_fk;
            
            if( !empty($table) && !empty($fk) 
                    && !empty($craftedData[ $table ][ $fk ])
                    && !empty($structures[ $table ])
            ){
                $motherWitch->craft( 
                    $craftedData[ $table ][ $fk ],
                    $structures[ $table ]
                );
            }

            if( $craftLevel == "*" ){
                $craftSubLevel = $craftLevel;
            }
            else {
                $craftSubLevel = $craftLevel - 1;
            }

            if( $craftSubLevel == "*" || $craftSubLevel > 0 ){
                $this->setParentsCraftData( $motherWitch, $craftSubLevel, $craftedData, $structures );
            }
        }
        
        return true;
    }
    
}
