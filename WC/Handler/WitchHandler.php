<?php 
namespace WC\Handler;

use WC\WitchCase;
use WC\Witch;
use WC\Datatype\ExtendedDateTime;

class WitchHandler
{

    /**
     * Witch factory class, implements witch whith data provided
     * @param WitchCase $wc
     * @param array $data
     * @return self
     */
    static function createFromData(  WitchCase $wc, array $data ): Witch
    {
        $witch      = new Witch();
        $witch->wc  = $wc;
        
        foreach( Witch::FIELDS as $field ){
            $witch->properties[ $field ] = NULL;
        }

        $witch->properties = $data;
        
        $witch->propertiesRead();

        $witch->position    = [];
        

        $i = 1;
        while( isset($data['level_'.$i]) )
        {
            $witch->position[$i] = (int) $data['level_'.$i];
            $i++;
        }
        $witch->depth       = $i - 1; 
                
        if( $witch->depth == 0 ){
            $witch->mother = false;
        }
        
        return $witch;
    }


    /**
     * Update Object properties based of object var "properties"
     * @return void
     */
    static function propertiesRead( Witch $witch ): void
    {
        if( !is_null($witch->properties['id']) ){
            $witch->id = (int) $witch->properties['id'];
        }
        
        if( !is_null($witch->properties['name']) ){
            $witch->name = $witch->properties['name'];
        }
        
        if( !is_null($witch->properties['datetime']) ){
            $witch->datetime = new ExtendedDateTime($witch->properties['datetime']);
        }
        
        if( !is_null($witch->properties['site']) ){
            $witch->site = $witch->properties['site'];
        }
        
        if( !is_null($witch->properties['status']) ){
            $witch->statusLevel = (int) $witch->properties['status'];
        }
        
        $witch->status = null;
        
        return;
    }

}