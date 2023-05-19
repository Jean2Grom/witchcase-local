<?php
namespace WC;

use WC\DataAccess\WitchSummoning;
use WC\DataAccess\WitchCrafting;

class Cairn 
{
    const DEFAULT_WITCH = "current";
    
    /** WitchCase */
    var $wc;
    
    /** WitchSummoning */
    var $witchSummoning;
    
    /** Website */
    var $website;
    
    /** WitchCrafting */
    var $witchCrafting;
    
    private $witches;
    private $cauldron;
    private $crafts;
    private $override;
    
    var $configuration;
    
    function __construct( WitchCase $wc, array $summoningConfiguration, ?Website $website=null )
    {
        $this->wc       = $wc;        
        $this->website  = $website ?? $this->wc->website;
        
        $this->witches  = [];
        $this->cauldron = [];
        $this->crafts   = [];
        $this->override = [];
        
        $this->configuration    = $summoningConfiguration;
        foreach( $this->configuration as $refWitchName => $refWitchSummoning )
        {
            $unset = false;
            if( empty($refWitchSummoning) ){
                $unset = true;
            }
            
            if( !empty($refWitchSummoning['get']) )
            {
                $paramValue = $this->wc->request->param($refWitchSummoning['get'], 'get');
                if( $paramValue )
                {
                    $this->configuration[ $refWitchName ]['id'] = $paramValue;
                    unset($this->configuration[ $refWitchName ]['get']);
                }
                else {
                    $unset = true;
                }
            }
            
            if( !empty($refWitchSummoning['url']) )
            {
                $this->configuration[ $refWitchName ]['website_name']   = $this->website->name;
                $this->configuration[ $refWitchName ]['website_url']    = $this->website->urlPath;
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
        
        $this->wc->dump( $this->configuration );
        
        
        
        $this->witchSummoning   = new WitchSummoning( $this->wc, $summoningConfiguration, $this->website );
        $this->wc->dump( $this->witchSummoning->configuration );
        
        //$this->witchCrafting    = new WitchCrafting( $this->wc, $this->witchSummoning->configuration, $this->website );    
        $this->witchCrafting    = new WitchCrafting( $this->wc, $this->configuration, $this->website );    
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
        return $this->witches[ $witchName ?? self::DEFAULT_WITCH ] ?? null; 
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
}
