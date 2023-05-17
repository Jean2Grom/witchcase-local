<?php
namespace WC;

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
    
    function __construct( WitchCase $wc, ?Website $website=null )
    {
        $this->wc       = $wc;        
        $this->website  = $website ?? $this->wc->website;
        
        $this->witches  = [];
        $this->cauldron = [];
        $this->crafts   = [];
        $this->override = [];
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
