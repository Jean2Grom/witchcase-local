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
        if( empty($configuration['id']) && empty($configuration['user']) ){
            return [];
        }
        
        $result = CauldronDA::cauldronRequest($wc, $configuration);
        
        if( $result === false ){
            return false;
        }
        
        return self::instanciate($wc, $configuration, $result);
    }

    
    /**
     * 
     */
    private static function instanciate( WitchCase $wc, $configuration, $result )
    {
        $return         = [];

        $cauldronsList  = [];
        $depthArray     = [];
        foreach( range(0, $wc->caudronDepth) as $d ){
            $depthArray[ $d ] = [];
        }
        
        $conditions     = [];
        foreach( $configuration["id"] ?? [] as $typeConfiguration ){            
            if( !empty($typeConfiguration['id']) ){
                $conditions[ 'id_'.$typeConfiguration['id'] ] = [ 'id'  => $typeConfiguration['id'] ];
            }
            else {
                foreach( $typeConfiguration as $typeConfigurationItem ){
                    $conditions[  'id_'.$typeConfigurationItem['id'] ] = [ 'id'  => $typeConfigurationItem['id'] ];
                }
            }
        }
        if( !empty($configuration['user']) ){
            $conditions[ 'user' ] = [ 
                'identifier_value_table'    => 'user__connexion', 
                'identifier_value_id'       => $wc->user->id, 
            ];
        }
        
        foreach( $result as $row )
        {
            $id                                             = $row['id'];
            $cauldronsList[ $id ]                           = $cauldronsList[ $id ] 
                                                                ?? self::createFromData( $wc, $row );
            $depthArray[ $cauldronsList[ $id ]->depth ][]   = $id;
            //$cauldronsList[ $id ]   = $cauldron;
            
            foreach( $conditions as $witchRef => $conditionsItem )
            {
                $matched = true;
                foreach( $conditionsItem as $field => $value ){
                    
                    if( $row[ $field ] !== $value )
                    {
                        $matched = false;
                        break;
                    }
                }
                
                if( $matched ){
                    $witches[ $witchRef ] = $cauldronsList[ $id ];
                }
            }
        }

$wc->dump( $depthArray );
$wc->dump( $cauldronsList );
return;
        
                
        for( $i=0; $i < $wc->depth; $i++ ){
            foreach( $depthArray[ $i ] as $potentialMotherId ){
                foreach( $depthArray[ ($i+1) ] as $potentialDaughterId ){
                    if( $witchesList[ $potentialMotherId ]->isMotherOf( $witchesList[ $potentialDaughterId ] ) ){
                        $witchesList[ $potentialMotherId ]->addDaughter( $witchesList[ $potentialDaughterId ] );
                    }
                }
            }
        }
        
        foreach( $configuration as $type => $typeConfiguration )
        {
            if( $type === 'user' ){
                $witchRefConfJoins = [ 'user' => $typeConfiguration ];
            }
            else {
                $witchRefConfJoins = $typeConfiguration;
            }
            
            foreach( $witchRefConfJoins as $witchRefConf )
            {
                if( empty($witchRefConf['entries']) ){
                    continue;                    
                }
                
                $witchRef = array_keys($witchRefConf['entries'])[0];
                
                if( !isset($witches[ $witchRef ]) ){
                    continue;
                }
                
                if( !empty($witchRefConf['children']) && !empty($witchRefConf['children']['depth']) )
                {
                    $depthLimit = $wc->depth - $witches[ $witchRef ]->depth;
                    if( $witchRefConf['children']['depth'] !== '*' 
                            && (int) $witchRefConf['children']['depth'] < $depthLimit 
                    ){
                        $depthLimit = (int) $witchRefConf['children']['depth'];
                    }
                    
                    self::initChildren( $witches[ $witchRef ], $depthLimit );
                }
            }
        }
        
        
        foreach( $configuration as $type => $typeConfiguration )
        {
            if( $type === 'user' ){
                $witchRefConfJoins = [ 'user' => $typeConfiguration ];
            }
            else {
                $witchRefConfJoins = $typeConfiguration;
            }

            foreach( $witchRefConfJoins as $witchRefConf )
            {
                if( empty($witchRefConf['entries']) ){
                    continue;                
                }
                
                $witchRef = array_keys($witchRefConf['entries'])[0];
                
                if( !isset($witches[ $witchRef ]) ){
                    continue;
                }

                if( !empty($witchRefConf['sisters']) && !empty($witchRefConf['sisters']['depth']) )
                {
                    $depthLimit = $wc->depth - $witches[ $witchRef ]->depth;
                    if( $witchRefConf['sisters']['depth'] !== '*' 
                            && (int) $witchRefConf['sisters']['depth'] < $depthLimit 
                    ){
                        $depthLimit = (int) $witchRefConf['sisters']['depth'];
                    }

                    if( is_null($witches[ $witchRef ]->sisters) ){
                        $witches[ $witchRef ]->sisters = [];
                    }

                    if( !empty($witches[ $witchRef ]->mother) && !empty($witches[ $witchRef ]->mother->daughters) ){
                        foreach( $witches[ $witchRef ]->mother->daughters as $daughterWitch )
                        {
                            if( $witches[ $witchRef ]->id !== $daughterWitch->id ){
                                $witches[ $witchRef ]->addSister($daughterWitch);
                            }
                            self::initChildren( $daughterWitch, $depthLimit );
                        }
                    }
                }
            }
        }
                
        return $witches;
    }


    /**
     * Witch factory class, implements witch whith data provided
     * @param WitchCase $wc
     * @param array $data
     * @return Cauldron
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