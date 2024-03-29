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
     * 
     */
    static function fetch( WitchCase $wc, array $configuration )
    {
        
        $result = CauldronDA::cauldronRequest($wc, $configuration);
        
        if( $result === false ){
            return false;
        }
        
        return self::instanciate($wc, $configuration, $result);
    }
    
    /**
     * 
     */
    private static function instanciate( WitchCase $wc, $configuration, $result ): array
    {
        $return         = [];
        $cauldronsList  = [];
        $depthArray     = [];
        foreach( range(0, $wc->caudronDepth) as $d ){
            $depthArray[ $d ] = [];
        }
        
        foreach( $result as $row )
        {
            $id                     = $row['id'];
            $cauldronsList[ $id ]   = $cauldronsList[ $id ] ?? self::createFromData( $wc, $row );
            
            IngredientHandler::createFromData( $cauldronsList[ $id ], $row );
            if( !in_array($id, $depthArray[ $cauldronsList[ $id ]->depth ]) ){
                $depthArray[ $cauldronsList[ $id ]->depth ][] = $id;
            }

            foreach( $configuration as $conf ){
                if( $conf === $id ){
                    $return[ $conf ] = $cauldronsList[ $id ];
                }
            }

            if(  in_array('user', $configuration) 
                &&  empty($return['user'])
                &&  $row['identifier_value_table'] === 'user__connexion'
                &&  $row['identifier_value_id'] === $wc->user->id 
            ){
                $return['user'] = $cauldronsList[ $id ];
            }

        }
        
        for( $i=0; $i < $wc->caudronDepth; $i++ ){
            foreach( $depthArray[ $i ] as $potentialParentId ){
                foreach( $depthArray[ ($i+1) ] as $potentialDaughterId ){
                    if( $cauldronsList[ $potentialParentId ]->isParentOf($cauldronsList[ $potentialDaughterId ]) ){
                        self::setParenthood($cauldronsList[ $potentialParentId ], $cauldronsList[ $potentialDaughterId ]);
                    }
                }
            }
        }
        
        return $return;
    }


    /**
     * Cauldron factory class, implements Cauldron with data provided
     * @param WitchCase $wc
     * @param array $data
     * @return Cauldron
     */
    static function createFromData(  WitchCase $wc, array $data ): Cauldron
    {
        $cauldron       = new Cauldron();
        $cauldron->wc   = $wc;
        
        foreach( Cauldron::FIELDS as $field ){
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
        if( !is_null($cauldron->properties['id']) ){
            $cauldron->id = (int) $cauldron->properties['id'];
        }
        
        if( !is_null($cauldron->properties['status']) ){
            $cauldron->status = $cauldron->properties['status'] === 0? 'draft': 'archive';
        }
        
        if( !is_null($cauldron->properties['content_key']) ){
            $cauldron->contentCauldronID = (int) $cauldron->properties['content_key'];
        }
        
        if( !is_null($cauldron->properties['name']) ){
            $cauldron->name = $cauldron->properties['name'];
        }
        
        if( !is_null($cauldron->properties['resume']) ){
            $cauldron->resume = $cauldron->properties['resume'];
        }
        
        if( !is_null($cauldron->properties['data']) ){
            $cauldron->data = json_decode( $cauldron->properties['data'] );
        }

        if( !is_null($cauldron->properties['priority']) ){
            $cauldron->priority = (int) $cauldron->properties['priority'];
        }
        
        if( !is_null($cauldron->properties['datetime']) ){
            $cauldron->datetime = new ExtendedDateTime($cauldron->properties['datetime']);
        }
                
        return;
    }
        
    static function setParenthood( Cauldron $parent, Cauldron $child ): void
    {
        if( $parent->children && in_array($child->id, array_keys( $parent->children )) ){
            return;
        }

        $parent->children   = array_replace( $parent->children ?? [], [$child->id => $child] );
        $child->parent      = $parent;
        
        return;
    }

}