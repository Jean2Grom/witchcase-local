<?php 
namespace WC\Handler;

use WC\WitchCase;
use WC\Cauldron;
use WC\Ingredient;
use WC\DataAccess\CauldronDataAccess AS DataAccess;
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
        $result = DataAccess::cauldronRequest($wc, $configuration);
        
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
        foreach( range(0, $wc->cauldronDepth) as $d ){
            $depthArray[ $d ] = [];
        }
        
        foreach( $result as $row )
        {
            $id                     = $row['id'];
            $cauldronsList[ $id ]   = $cauldronsList[ $id ] ?? self::createFromData( $wc, $row );
            
            IngredientHandler::createFromDBRow( $cauldronsList[ $id ], $row );
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
                &&  $row['i_name'] === 'user__connexion'
                &&  $row['i_value'] === $wc->user->id 
            ){
                $return['user'] = $cauldronsList[ $id ];
            }

        }
        
        for( $i=0; $i < $wc->cauldronDepth; $i++ ){
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
        
        for( $i=1; $i <= $wc->cauldronDepth; $i++ ){
            $cauldron->properties[ 'level_'.$i ] = $data[ 'level_'.$i ] ?? null;
        }

        self::readProperties( $cauldron );

        return $cauldron;
    }  

    /**
     * Update  Object current state based on var "properties" (database direct fields) 
     * @return void
     */
    static function readProperties( Cauldron $cauldron, bool $excludePostition=false ): void
    {
        $cauldron->id = null;
        if( isset($cauldron->properties['id']) && ctype_digit(strval($cauldron->properties['id'])) ){
            $cauldron->id = (int) $cauldron->properties['id'];
        }
        
        $cauldron->status = null;
        if( isset($cauldron->properties['status']) ){
            $cauldron->status = $cauldron->properties['status'];
        }
        
        $cauldron->targetID = null;
        if( isset($cauldron->properties['target']) && ctype_digit(strval($cauldron->properties['target'])) ){
            $cauldron->targetID = (int) $cauldron->properties['target'];
        }
        
        if( $cauldron->targetID !== $cauldron->target?->id ){
            $cauldron->target   = null;
        }

        $cauldron->name = null;
        if( isset($cauldron->properties['name']) ){
            $cauldron->name = $cauldron->properties['name'];
        }
        
        $cauldron->data = null;
        if( isset($cauldron->properties['data']) )
        {
            $cauldron->data = json_decode( $cauldron->properties['data'] );
            $cauldron->properties['data'] = json_encode($cauldron->data);
        }

        $cauldron->priority = 0;
        if( isset($cauldron->properties['priority']) ){
            $cauldron->priority = (int) $cauldron->properties['priority'];
        }
        
        $cauldron->datetime = null;
        if( isset($cauldron->properties['datetime']) ){
            $cauldron->datetime = new ExtendedDateTime($cauldron->properties['datetime']);
        }

        if( $excludePostition ){
            return;
        }
        
        $cauldron->position    = [];
        
        $i = 1;
        while( 
            isset($cauldron->properties[ 'level_'.$i ]) 
            && ctype_digit(strval( $cauldron->properties['level_'.$i] )) 
        ){
            $cauldron->position[ $i ] = (int) $cauldron->properties[ 'level_'.$i ];
            $i++;
        }
        $cauldron->depth = $i - 1; 
        
        return;
    }


    /**
     * Update var "properties" (database direct fields) based on Object current state 
     * @return void
     */
    static function writeProperties( Cauldron $cauldron ): void
    {
        $cauldron->properties= [];

        if( isset($cauldron->id) && is_int($cauldron->id) ){
            $cauldron->properties['id'] = $cauldron->id;
        }        
        
        if( in_array( $cauldron->status, 
            [Cauldron::STATUS_PUBLISHED, Cauldron::STATUS_DRAFT, Cauldron::STATUS_ARCHIVED] )
        ){
            $cauldron->properties['status'] = $cauldron->status;
        }
        
        if( $cauldron->target ){
            $cauldron->properties['target'] = $cauldron->target->id;
        }
        elseif( $cauldron->targetID ){
            $cauldron->properties['target'] = $cauldron->targetID;
        }
        else {
            $cauldron->properties['target'] = null;
        }
        
        if( isset($cauldron->name) ){
            $cauldron->properties['name'] = $cauldron->name;
        }
        
        if( isset($cauldron->data) )
        {
            $jsonData = json_encode( $cauldron->data );

            if( $jsonData ){
                $cauldron->properties['data'] = $jsonData;
            }
        }

        $cauldron->properties['priority'] = $cauldron->priority ?? 0;
        
        if( isset($cauldron->datetime) ){
            $cauldron->properties['datetime'] = $cauldron->datetime->format('Y-m-d H:i:s');
        }

        $i = 1;
        while( 
            isset($cauldron->position[ $i ]) 
            && ctype_digit(strval( $cauldron->position[$i] )) 
        ){
            $cauldron->properties[ 'level_'.$i ] = $cauldron->position[ $i ];
            $i++;
        }
        $cauldron->depth = $i - 1; 
        
        for( $j=$i; $j<=$cauldron->wc->cauldronDepth; $j++ ){
            $cauldron->properties[ 'level_'.$j ] = null;
        }

        return;
    }

    static function setParenthood( Cauldron $parent, Cauldron $child ): bool
    {
        if( !in_array($child, $parent->children) )
        {
            $child->parent      = $parent;
            $parent->children[] = $child;
            return true;
        }
        
        return false;
    }

    static function setIngredient( Cauldron $cauldron, Ingredient $ingredient ): bool
    {
        if( !in_array($ingredient, $cauldron->ingredients) )
        {
            $ingredient->cauldron       = $cauldron;
            $ingredient->cauldronID     = $cauldron->id;
            $cauldron->ingredients[]    = $ingredient;
            return true;
        }
        
        return false;
    }


    static function getDraftFolder( Cauldron $cauldron ): Cauldron {
        return self::getNamedFolder( $cauldron, Cauldron::DRAFT_FOLDER_NAME );
    }

    static function getArchiveFolder( Cauldron $cauldron ): Cauldron {
        return self::getNamedFolder( $cauldron, Cauldron::ARCHIVE_FOLDER_NAME );
    }

    private static function getNamedFolder( Cauldron $cauldron, string $folderName ): Cauldron
    {
        foreach( $cauldron->children as $child ){
            if( $child->name === $folderName
                && $child->data->structure === "folder" ){
                return $child;
            }
        }
        
        $params = [
            'name'  =>  $folderName,
            'data'  =>  json_encode([ "structure" => "folder" ]),
        ];

        $folder = self::createFromData( $cauldron->wc, $params );
        $cauldron->addCauldron( $folder );
        $folder->save();

        return $folder;
    }


    static function createDraft( Cauldron $cauldron ): Cauldron
    {
        self::writeProperties( $cauldron );

        $draftProperties            = $cauldron->properties;
        $draftProperties['target']  = $cauldron->id;
        $draftProperties['status']  = Cauldron::STATUS_DRAFT;

        unset( $draftProperties['id'] );
        
        $draft          = self::createFromData( $cauldron->wc, $draftProperties );
        $draft->target  = $cauldron;
        
        self::createDraftContent( $cauldron, $draft );
        
        return $draft;
    }

    static private function createDraftContent( Cauldron $cauldron, Cauldron $draft )
    {
        foreach( $cauldron->content() as $content )
        {
            // Ingredient case
            if( get_class($content) !== get_class($cauldron) )
            {
                IngredientHandler::writeProperties($content);
                $draftContentProperties = $content->properties;
                unset( $draftContentProperties['id'] );
                unset( $draftContentProperties['cauldron_fk'] );

                IngredientHandler::createFromData( $draft, $content->type, $draftContentProperties );
            }
            // Cauldron case            
            else 
            {
                self::writeProperties( $content );
                $draftContentProperties = $content->properties;
                unset( $draftContentProperties['id'] );
                
                $draftContent = self::createFromData( $cauldron->wc, $draftContentProperties );

                self::setParenthood( $draft, $draftContent );
                self::createDraftContent( $content, $draftContent );
            }
        }

        return;
    }
}