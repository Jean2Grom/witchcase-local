<?php
namespace WC;

class Cairn 
{
    const DEFAULT_WITCH = "current";


    /** WitchCase */
    var $wc;
    
    /** Website */
    var $website;
    
    var $witches;
    var $cauldron;
    
    function __construct( WitchCase $wc, ?Website $website=null )
    {
        $this->wc       = $wc;        
        $this->website  = $website ?? $this->wc->website;
        
        $this->witches  = [];
        $this->cauldron = [];
    }
    
    function addWitches( array $witches )
    {
        foreach( $witches as $witchName => $witch )
        {
            if( $witch instanceof Witch ){
                $this->witches[ $witchName ] = $witch;
            }
            
        }
        
        return $this; 
    }
    
    function getWitches(){
        return $this->witches; 
    }
    
    function witch( ?string $witchName=null ){
        return $this->witches[ $witchName ?? self::DEFAULT_WITCH ] ?? null; 
    }
    
    function __get( string $witchName ){
        return $this->witches[ $witchName ] ?? null; 
    }
    
    function addData( array $craftData )
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
    
    function readData( string $table, int $id ){
        return $this->cauldron[ $table ][ $id ] ?? null;
    }
    
    function unsetData( string $table, int $id ): bool
    {
        if( isset($this->cauldron[ $table ][ $id ]) )
        {
            unset($this->cauldron[ $table ][ $id ]);
            return true;
        }
        
        return false;
    }
}
