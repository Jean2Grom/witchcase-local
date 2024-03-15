<?php 
namespace WC\Handler;

use WC\WitchCase;
use WC\Cauldron;
use WC\DataAccess\Cauldron AS CauldronDA;
use WC\Datatype\ExtendedDateTime;

class CauldronHandler
{
    const STATUS_ARRAY = [
        'content',
        'draft',
        'archive',
    ];

    /**
     * Witch factory class, implements witch whith data provided
     * @param WitchCase $wc
     * @param array $data
     * @return self
     */
    static function createFromData(  WitchCase $wc, array $data ): Cauldron
    {
        $cauldron       = new Cauldron();
        $cauldron->wc   = $wc;
        
        foreach( CauldronDA::FIELDS as $field ){
            $cauldron->properties[ $field ] = $data[ $field ] ?? null;
        }
        
        self::readProperties( $cauldron );
        
        $cauldron->position    = [];
        
        $i = 1;
        while( isset($data['level_'.$i]) )
        {
            $cauldron->position[$i] = (int) $data['level_'.$i];
            $i++;
        }
        $cauldron->depth       = $i - 1; 
        
        if( $cauldron->depth == 0 ){
            $cauldron->parent = false;
        }
        
        return $cauldron;
    }

    /**
     * Update Object properties based of object var "properties"
     * @return void
     */
    static function readProperties( Cauldron $cauldron ): void
    {
        if( !empty($cauldron->properties['id']) ){
            $cauldron->id = (int) $cauldron->properties['id'];
        }
        
        if( isset($cauldron->properties['status']) ){
            $cauldron->status = $cauldron->properties['status'] === 0? 'draft': 'archive';
        }
        
        if( !empty($cauldron->properties['content_key']) ){
            $cauldron->contentID = (int) $cauldron->properties['content_key'];
        }
        
        if( !empty($cauldron->properties['name']) ){
            $cauldron->name = $cauldron->properties['name'];
        }
        
        if( !empty($cauldron->properties['data']) ){
            $cauldron->data = json_decode( $cauldron->properties['data'], true );
        }
        
        if( !empty($cauldron->properties['datetime']) ){
            $cauldron->datetime = new ExtendedDateTime($cauldron->properties['datetime']);
        }
                
        return;
    }
        

}