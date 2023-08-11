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
                ->addWitches( WitchSummoning::summon($this->wc, $this->configuration) )
                ->addData( WitchCrafting::readCraftData($this->wc, $this->configuration, $this->getWitches() ));
    }
    
    function sabbath()
    {
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
$this->wc->debug( $position, "searchFromPosition");
//$this->wc->debug( count($position), "murf", 2 );
//$this->wc->debug( $this->witches, "xxx", 2 );
        
        foreach( $this->witches as $entry => $witch )
        {
            if( $witch->position == $position ){
                return $witch;
            }
            elseif( count($position) > count($witch->position) )
            {
                /*
$this->wc->debug( "Descendant" );
                $descendant = true;
                foreach( $witch->position as $level => $value ){
                    if( $position[ $level ] != $value )
                    {
                        $descendant = false;
                        break;
                    }
                }
                
                if( $descendant )
                {
                    $witchBuffer = $witch;
                    for( $level=$witch->depth+1; $level <= count($position); $level++ )
                    {
                        if( $witchBuffer->depth >= $level ){
                            break;
                        }
                        
                        foreach( $witchBuffer->daughters as $daughter )
                        {
                            if( $daughter->position == $position ){
                                return $daughter;
                            }
                            elseif($daughter->position[ $level ] == $position[ $level ])
                            {
                                $witchBuffer = $daughter;
                                break;
                            }
                        }
                    }
                }
                */
            }
            elseif( count($position) < count($witch->position) )
            {
$this->wc->debug( $witch->name, $entry);
$this->wc->debug( "Ancestor" );
$this->wc->debug( $witch->position, "Witch Position");
            
                
                $ancestor = true;
                foreach( $position as $level => $value ){
                    if( $witch->position[ $level ] != $value )
                    {
                        $ancestor = false;
                        break;
                    }
                }
                
                if( $ancestor )
                {
                    $witchBuffer = $witch->mother;
                    for( $level=$witch->depth-1; $level > 0; $level-- )
                    {
                        if( $witchBuffer->position == $position ){
                            return $witchBuffer;
                        }
                        
                        if( !$witchBuffer->mother || $witchBuffer->mother->position[ $level ] !== $position[ $level ] ){
                            break;
                        }
                        else {
                            $witchBuffer = $witchBuffer->mother;
                        }
                    }
                }                
            }
        }
        
        
        return null;
    }
}
