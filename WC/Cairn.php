<?php
namespace WC;

use WC\DataAccess\WitchSummoning;
use WC\DataAccess\WitchCrafting;

class Cairn 
{
    const DEFAULT_WITCH = "current";
    
    /** WitchCase */
    var $wc;
    
    /** Website */
    var $website;
    
    private $witches;
    private $cauldron;
    private $crafts;
    private $override;
    
    var $invokations;
    
    var $configuration;
    var $configuration2;
    
    function __construct( WitchCase $wc, array $summoningConfiguration, ?Website $forcedWebsite=null )
    {
        $this->wc       = $wc;
        $this->website  = $forcedWebsite ?? $this->wc->website;
        
        $this->witches  = [];
        $this->cauldron = [];
        $this->crafts   = [];
        $this->override = [];
        
        $this->invokations  = [];
        
        $this->configuration    = self::prepareConfiguration($this->website, $summoningConfiguration);
        $this->configuration2   = self::prepareConfiguration2($this->website, $summoningConfiguration);
//$this->wc->debug($this->website->name);        
//$this->wc->dump($this->configuration);        
//$this->wc->dump($this->configuration2);
    }
    
    static function prepareConfiguration2(  Website $website, array $rawConfiguration ): array
    {
        $arboConf = function ($init, $new) {
            if( is_array($new) )
            {
                $innerDepth      = 1;
                $innerCraft = false;
                if( is_array($init) )
                {
                    $innerDepth      = $init['depth'];
                    $innerCraft = $init['craft'];
                }

                if( $innerDepth == '*' || $new['depth'] == '*' ){
                    $innerDepth = '*';
                }
                elseif( !empty($new['depth']) && $new['depth'] >= $innerDepth ){
                    $innerDepth = $new['depth'];
                }

                if( $innerCraft == '*' || $new['craft'] == '*' ){
                    $innerCraft = '*';
                }
                elseif( !empty($new['craft']) && $new['craft'] >= $innerCraft ){
                    $innerCraft = $new['craft'];
                }

                return [
                    'depth' => $innerDepth,
                    'craft' => $innerCraft,
                ];
            }
            else {
                return $init;
            }
        };
        
        $ids    = [];
        $urls   = [];
        $user   = [];        
        foreach( $rawConfiguration as $refWitchName => $refWitchSummoning )
        {
            
            if( !empty($refWitchSummoning['id']) )
            {
                $index = 'id_'.$refWitchSummoning['id'];
                
                $entries    = [];
                $craft      = $refWitchSummoning['craft'] ?? true;
                
                $parents    = false;
                $sisters    = false;
                $children   = false;
                if( isset($ids[ $index ]) )
                {
                    $entries    = $ids[ $index ]['entries'];
                    $craft      = $craft || $ids[ $index ]['craft'];
                    
                    $parents    = $ids[ $index ]['parents'];
                    $sisters    = $ids[ $index ]['sisters'];
                    $children   = $ids[ $index ]['children'];
                }
                
                $ids[ $index ] = [
                    'id'        => (int) $refWitchSummoning['id'],
                    'craft'     => $craft,
                    'entries'   => array_merge($entries, [ $refWitchName => $refWitchSummoning['invoke'] ?? false ]),
                    'parents'   => $arboConf( $parents, $refWitchSummoning['parents'] ?? false ),
                    'sisters'   => $arboConf( $sisters, $refWitchSummoning['sisters'] ?? false ),
                    'children'  => $arboConf( $children, $refWitchSummoning['children'] ?? false ),
                ];
            }
            
            if( !empty($refWitchSummoning['get']) )
            {
                $paramValue = $website->wc->request->param($refWitchSummoning['get'], 'get', FILTER_VALIDATE_INT);
                if( $paramValue )
                {
                    $index = 'id_'.$paramValue;
                    
                    $entries    = [];
                    $craft      = $refWitchSummoning['craft'] ?? true;

                    $parents    = false;
                    $sisters    = false;
                    $children   = false;
                    if( isset($ids[ $index ]) )
                    {
                        $entries    = $ids[ $index ]['entries'];
                        $craft      = $craft || $ids[ $index ]['craft'];

                        $parents    = $ids[ $index ]['parents'];
                        $sisters    = $ids[ $index ]['sisters'];
                        $children   = $ids[ $index ]['children'];
                    }

                    $ids[ $index ] = [
                        'id'        => (int) $paramValue,
                        'craft'     => $craft,
                        'entries'   => array_merge($entries, [ $refWitchName => $refWitchSummoning['invoke'] ?? false ]),
                        'parents'   => $arboConf( $parents, $refWitchSummoning['parents'] ?? false ),
                        'sisters'   => $arboConf( $sisters, $refWitchSummoning['sisters'] ?? false ),
                        'children'  => $arboConf( $children, $refWitchSummoning['children'] ?? false ),
                    ];
                }
            }
            
            if( !empty($refWitchSummoning['url']) )
            {
                $urlData = $website->getUrlSearchParameters();
                if( !empty($refWitchSummoning['site']) ){
                    $urlData['site'] = $refWitchSummoning['site'];
                }
                if( is_string($refWitchSummoning['url']) ){
                    $urlData['url'] = $refWitchSummoning['url'];
                }
                
                $index = md5($urlData['site'].':'.$urlData['url']);
                
                $entries    = [];
                $craft      = $refWitchSummoning['craft'] ?? true;
                
                $parents    = false;
                $sisters    = false;
                $children   = false;
                if( isset($urls[ $index ]) )
                {
                    $entries    = $urls[ $index ]['entries'];
                    $craft      = $craft || $urls[ $index ]['craft'];
                    
                    $parents    = $urls[ $index ]['parents'];
                    $sisters    = $urls[ $index ]['sisters'];
                    $children   = $urls[ $index ]['children'];
                }
                
                $urls[ $index ] = [
                    'site'      => $urlData['site'],
                    'url'       => $urlData['url'],
                    'craft'     => $craft,
                    'entries'   => array_merge($entries, [ $refWitchName => $refWitchSummoning['invoke'] ?? false ]),
                    'parents'   => $arboConf( $parents, $refWitchSummoning['parents'] ?? false ),
                    'sisters'   => $arboConf( $sisters, $refWitchSummoning['sisters'] ?? false ),
                    'children'  => $arboConf( $children, $refWitchSummoning['children'] ?? false ),
                ];
            }
            
            if( !empty($refWitchSummoning['user']) )
            {
                $entries    = [];
                $craft      = $refWitchSummoning['craft'] ?? true;
                
                $parents    = false;
                $sisters    = false;
                $children   = false;
                if( !empty($user) )
                {
                    $entries    = $user['entries'];
                    $craft      = $craft || $user['craft'];
                    
                    $parents    = $user['parents'];
                    $sisters    = $user['sisters'];
                    $children   = $user['children'];
                }
                
                $user = [
                    'craft'     => $craft ,
                    'entries'   => array_merge($entries, [ $refWitchName => $refWitchSummoning['invoke'] ?? false ]),
                    'parents'   => $arboConf( $parents, $refWitchSummoning['parents'] ?? false ),
                    'sisters'   => $arboConf( $sisters, $refWitchSummoning['sisters'] ?? false ),
                    'children'  => $arboConf( $children, $refWitchSummoning['children'] ?? false ),
                ];
            }
        }
        
        $configuration = [
            'id'    => $ids,
            'url'   => $urls,
            'user'  => $user,
        ];
        
        foreach( $configuration as $type => $typeConfiguration ){
            foreach( $typeConfiguration as $refWitchName => $refWitchSummoning ){
                if( !empty($refWitchSummoning['sisters']) && empty($refWitchSummoning['parents']) ){
                    $configuration[ $type ][ $refWitchName ]['parents'] = [
                        "depth" => 1,
                        "craft" => false
                    ];
                }
            }
        }
        
        return $configuration;
    }
    
    static function prepareConfiguration(  Website $website, array $rawConfiguration ): array
    {
        $configuration = $rawConfiguration;
        foreach( $configuration as $refWitchName => $refWitchSummoning )
        {
            $unset = false;
            if( empty($refWitchSummoning) ){
                $unset = true;
            }
            
            if( !empty($refWitchSummoning['get']) )
            {
                $paramValue = $website->wc->request->param($refWitchSummoning['get'], 'get');
                if( $paramValue )
                {
                    $configuration[ $refWitchName ]['id'] = $paramValue;
                    unset($configuration[ $refWitchName ]['get']);
                }
                else {
                    $unset = true;
                }
            }
            
            if( !empty($refWitchSummoning['url']) ){
                $configuration[ $refWitchName ] = array_replace($configuration[ $refWitchName ], $website->getUrlSearchParameters());
            }
                        
            if( $unset )
            {
                unset($configuration[ $refWitchName ]);
                continue;
            }
            
            foreach( $refWitchSummoning as $refWitchSummoningParam => $refWitchSummoningValue ){
                if( is_array($refWitchSummoningValue) ){
                    foreach( $refWitchSummoningValue as $refWitchSummoningValueKey => $refWitchSummoningValueItem ){
                        if( is_numeric($refWitchSummoningValueItem) ){
                            $configuration[ $refWitchName ][ $refWitchSummoningParam ][ $refWitchSummoningValueKey ] = (integer) $refWitchSummoningValueItem;
                        }
                    }
                }
            }
        }
        
        foreach( $configuration as $refWitchName => $refWitchSummoning ){
            if( !empty($refWitchSummoning['sisters']) && empty($refWitchSummoning['parents']) ){
                $configuration[ $refWitchName ]['parents'] = [
                    "depth" => 1,
                    "craft" => false
                ];
            }
        }
        
        return $configuration;
    }
    
    function summon()
    {
        return $this
                ->addWitches( WitchSummoning::summon2($this->wc, $this->configuration2) )
                ->addData( WitchCrafting::readCraftData($this->wc, $this->configuration, $this->getWitches() ));
    }
    
    function sabbath()
    {
        $this->wc->dump( WitchCrafting::readCraftData2($this->wc, $this->configuration2, $this->getWitches() ), "NEW" );
        
        foreach( $this->configuration as $refWitch => $witchConf ){
            if( $this->witch( $refWitch ) )
            {
                if( empty($witchConf['invoke']) ){
                    continue;
                }
                
                if( is_string($witchConf['invoke']) ){
                    $this->invokations[ $refWitch ] = $this->witch($refWitch)->invoke( $witchConf['invoke'] );
                }
                else {
                    $this->invokations[ $refWitch ] = $this->witch($refWitch)->invoke();
                }
            }
        }
        
        return true;
    }
    
    function invokation( ?string $witchConfRef=null ): string
    {
        $ref = $witchConfRef ?? self::DEFAULT_WITCH;
        
        return  $this->invokations[ $ref ] ?? "";
    }
    
    function addWitches( array $witches ): self
    {
        foreach( $witches as $witchName => $witch )
        {
            if( $witch instanceof Witch ){
                $this->witches[ $witchName ] = $witch;
            }
            
        }
        
        return $this; 
    }
    
    function getWitches(): array {
        return $this->witches; 
    }
    
    function witch( ?string $witchName=null ){
        return  $this->witches[ $witchName ?? self::DEFAULT_WITCH ] 
                    ?? Witch::createFromData( $this->wc, [ 'name' => "ABSTRACT 404 WITCH", 'invoke' => '404' ] ); 
    }
    
    function __get( string $witchName ){
        return $this->witches[ $witchName ] ?? null; 
    }
    
    function addData( array $craftData ): self
    {
$this->wc->dump( $craftData, "OLD" );
        
        foreach( $craftData as $table => $craftDataItem )
        {
            $this->cauldron[ $table ] = $this->cauldron[ $table ] ?? [];
            
            foreach( $craftDataItem as $id => $data ){
                $this->cauldron[ $table ][ $id ] = array_replace($this->cauldron[ $table ][ $id ] ?? [], $data);
            }
        }
        
        return $this;
    }
    
    function readData( string $table, int $id )
    {
        $this->cauldron[ $table ] = $this->cauldron[ $table ] ?? [];
        
        return $this->cauldron[ $table ][ $id ] ?? null;
    }
    
    function unsetData( string $table, int $id ): bool
    {
        $this->cauldron[ $table ] = $this->cauldron[ $table ] ?? [];
        
        if( isset($this->cauldron[ $table ][ $id ]) )
        {
            unset($this->cauldron[ $table ][ $id ]);
            return true;
        }
        
        return false;
    }
    
    function remove( string $table, int $id ): bool
    {
        $this->crafts[ $table ] = $this->crafts[ $table ] ?? [];
        
        if( isset($this->crafts[ $table ][ $id ]) ){
            unset($this->crafts[ $table ][ $id ]);
        }
        
        return $this->unsetData($table, $id);
    }
    
    function craft( string $table, int $id ): Craft
    {
        $this->override[ $table ] = $this->override[ $table ] ?? [];
        if( !empty($this->override[ $table ][ $id ]) ){
            return $this->override[ $table ][ $id ];
        }
        
        $this->crafts[ $table ] = $this->crafts[ $table ] ?? [];
        
        if( !isset($this->crafts[ $table ][ $id ]) )
        {
            $this->cauldron[ $table ] = $this->cauldron[ $table ] ?? [];
            
            if( !isset($this->cauldron[ $table ][ $id ]) ){
                $this->cauldron[ $table ]   = array_replace($this->cauldron[ $table ], WitchCrafting::craftQueryFromIds( $this->wc, $table, [$id] ));
            }
            
            $this->crafts[ $table ][ $id ] = Craft::factory( $this->wc, (new Structure( $this->wc, $table )), $this->cauldron[$table][$id] );
        }
        
        return $this->crafts[ $table ][ $id ];
    }
    
    
    function setCraft( Craft $craft, string $table, int $id ): self
    {
        $this->override[ $table ]         = $this->override[ $table ] ?? [];
        $this->override[ $table ][ $id ]  = $craft;
        
        return $this;
    }
    
    
    function searchFromPosition( array $position ): ?Witch
    {
        foreach( $this->witches as $witch )
        {
            if( $witch->position == $position ){
                return $witch;
            }
            
            $witchBuffer    = $witch;
            $continue       = true;
            while( $continue && $witchBuffer )
            {
                $continue = false;
                foreach( $witchBuffer->position as $level => $value ){
                    if( !isset($position[ $level ]) || $position[ $level ] != $value )
                    {
                        $witchBuffer    = $witchBuffer->mother;
                        $continue       = true;
                        break;
                    }
                }
                
                if( $witchBuffer->position == $position ){
                    return $witchBuffer;
                }
                elseif( $continue ){
                    continue;
                }                
                
                foreach( $witchBuffer->daughters as $daughter ){
                    if( $daughter->position == $position ){
                        return $daughter;
                    }
                    else
                    {
                        $level = $witchBuffer->depth + 1;
                        if( $daughter->position[ $level ] == $position[ $level ] ) 
                        {
                            $witchBuffer    = $daughter;
                            $continue       = true;
                            break;
                        }
                    }
                }
                
                if( $witchBuffer->position == $position ){
                    return $witchBuffer;
                }
            }
        }
        
        return null;
    }
}
