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


    private static function instanciate( WitchCase $wc, $configuration, $result )
    {
        $witches        = [];
        $witchesList    = [];
        
        $depthArray = [];
        foreach( range(0, $wc->depth) as $d ){
            $depthArray[ $d ] = [];
        }
        
        $conditions     = [];
        $urlRefWiches   = [];
        foreach( $configuration["url"] as $typeConfiguration ){
            foreach( array_keys($typeConfiguration['entries']) as $witchRef )
            {
                $conditions[ $witchRef ] = [ 
                    'site'  => $typeConfiguration['site'],
                    'url'   => $typeConfiguration['url'],
                ];
                
                $urlRefWiches[] = $witchRef;
            }
        }
        foreach( $configuration["id"] as $typeConfiguration ){
            foreach( array_keys($typeConfiguration['entries']) as $witchRef ){
                $conditions[ $witchRef ] = [ 'id'  => $typeConfiguration['id'] ];
            }
        }
        if( !empty($configuration['user']) && !empty($result[0]['user_craft_fk']) ){
            foreach( array_keys($configuration['user']['entries']) as $witchRef ){
                $conditions[ $witchRef ] = [ 
                    'craft_table'  => $wc->user->connexionData['craft_table'], 
                    'craft_fk'     => $result[0]['user_craft_fk'], 
                ];
            }
        }
        
        foreach( $result as $row )
        {
            $id                             = $row['id'];
            $witch                          = Witch::createFromData( $wc, $row );
            $depthArray[ $witch->depth ][]  = $id;
            $witchesList[ $id ]             = $witch;
            
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
                    $witches[ $witchRef ] = $witch;
                }
            }
        }
        
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
        
        foreach( $urlRefWiches as $urlRefWichItem ){
            if( empty($witches[ $urlRefWichItem ]) ){
                $witches[ $urlRefWichItem ] = Witch::createFromData( $wc, [ 'name' => "ABSTRACT 404 WITCH", 'invoke' => '404' ] );
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