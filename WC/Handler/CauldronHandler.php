<?php 
namespace WC\Handler;

use WC\WitchCase;
use WC\Cauldron;
use WC\Ingredient;
use WC\DataAccess\CauldronDataAccess AS DataAccess;
use WC\Datatype\ExtendedDateTime;
use WC\Witch;

class CauldronHandler
{
    const STATUS_ARRAY = [
        'content',
        'draft',
        'archive',
    ];


    /**
     * Fetch cauldrons from configuration array
     * @var Witchcase $wc
     * @var array $configuration
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
     * PRIVATE instanciate Cauldrons and Ingredients from configuration and data access results
     * @var Witchcase $wc
     * @var array $configuration
     * @var array $result
     * @return array 
     */
    private static function instanciate( WitchCase $wc, array $configuration, array $result ): array
    {
        $return         = [];
        $cauldronsList  = [];
        $witchesList    = [];
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

            // Witch part
            $witchId = $row['w_id'] ?? null;
            if( !$witchId || !empty($witchesList[ $witchId ]) ){
                continue;
            }

            $witch = $wc->cairn->searchById( $witchId );
            if( !$witch )
            {
                $witchData = [];
                foreach( Witch::FIELDS as $field ){
                    $witchData[ $field ] = $row[ "w_".$field ];
                }
                
                foreach( range(1, $wc->depth) as $i ){
                    $witchData[  "level_".$i ] = $row[ "w_level_".$i ];
                }
                
                $witch = WitchHandler::createFromData($wc, $witchData);
            }
            
            $witchesList[ $witchId ] = $witch;
        }

        // Link witches and cauldron objects
        foreach( $witchesList as $witch ){
            if( isset($cauldronsList[ $witch->cauldronId ]) )
            {
                $witch->cauldron = $cauldronsList[ $witch->cauldronId ];
                $cauldronsList[ $witch->cauldronId ]->witches[ $witch->id ] = $witch;
            }
        }
        foreach( $cauldronsList as $cauldron ){ 
            $cauldron->orderWitches();
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
     * @var Cauldron $cauldron
     * @var bool $excludePostition
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
        
        $cauldron->recipe = null;
        if( isset($cauldron->properties['recipe']) ){
            $cauldron->recipe = $cauldron->properties['recipe'];
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
     * @var Cauldron $cauldron
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
        
        if( isset($cauldron->recipe) ){
            $cauldron->properties['recipe'] = $cauldron->recipe;
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


    /**
     * Set a parenthood relation between two cauldrons
     * @var Cauldron $parent
     * @var Cauldron $child
     * @return bool
     */
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


    /**
     * Set ingrdient into a cauldron
     * @var Cauldron $cauldron
     * @var Ingredient $ingredient
     * @return bool
     */
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


    /**
     * Get the draft folder for a dedicated cauldron
     * @var Cauldron $cauldron
     * @return Cauldron 
     */
    static function getDraftFolder( Cauldron $cauldron ): Cauldron {
        return self::getWorkFolder( $cauldron, Cauldron::DRAFT_FOLDER_STRUCT );
    }

    
    /**
     * Get the archive folder for a dedicated cauldron
     * @var Cauldron $cauldron
     * @return Cauldron 
     */
    static function getArchiveFolder( Cauldron $cauldron ): Cauldron {
        return self::getWorkFolder( $cauldron, Cauldron::ARCHIVE_FOLDER_STRUCT );
    }


    /**
     * PRIVATE get work (draft or archive) folder for a dedicated cauldron
     * @var Cauldron $cauldron
     * @var string $folderStruct
     * @return Cauldron 
     */
    private static function getWorkFolder( Cauldron $cauldron, string $folderStruct ): Cauldron
    {
        foreach( $cauldron->children as $child ){
            //if( $child->data->structure === $folderStruct ){
            if( $child->recipe === $folderStruct ){
                return $child;
            }
        }
        
        $workFolderName = mb_strtoupper( $folderStruct );
        $cauldron->wc->debug($workFolderName);
        if( substr($workFolderName, 0, 3) === "WC-" ){
            $workFolderName = substr($workFolderName, 3);
        }
        if( substr($workFolderName, -7) === "-FOLDER" ){
            $workFolderName = substr($workFolderName, 0, -7);
        }
        
        $params = [
            'name'      =>  $workFolderName,
            'recipe'    =>  $folderStruct,
            //'data'  =>  json_encode([ "structure" => $folderStruct ]),
        ];
 
        $folder = self::createFromData( $cauldron->wc, $params );
        $cauldron->addCauldron( $folder );
        $folder->save();

        return $folder;
    }
    

    /**
     * Create a draft from a cauldron
     * @var Cauldron $cauldron
     * @return Cauldron
     */
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


    /**
     * PRIVATE create the draft contents
     * @var Cauldron $cauldron
     * @var Cauldron $draft
     * @return void
     */
    static private function createDraftContent( Cauldron $cauldron, Cauldron $draft ): void
    {
        foreach( $cauldron->contents() as $content )
        {
            // Ingredient case
            //if( get_class($content) !== get_class($cauldron) )
            if( is_a($content, Ingredient::class) )
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

 
    /**
     * @var WitchCase $wc
     * @return Cauldron|false
     */
    static function getStorageStructure(  WitchCase $wc, ?string $site=null, ?string $structure=null ): Cauldron|false 
    {
        $result = DataAccess::getStorageStructure( $wc );

        if( !$result ){
            return false;
        }

        $data           = self::instanciate($wc, [ 1 ], $result);
        $rootCauldron   = $data[ 1 ] ?? null;

        if( !$rootCauldron ){
            return false;
        }
        elseif( !$site ){
            return $rootCauldron;
        }

        $siteCauldron = false;
        foreach( $rootCauldron->children as $child ){
            if( $child->name === $site )
            {
                $siteCauldron = $child;
                break;
            }
        }
    
        if( !$siteCauldron )
        {
            $params = [
                'name'      =>  $site,
                'recipe'    =>  "folder",
                //'data'  =>  json_encode([ "structure" => "folder" ]),
            ];

            $siteCauldron = self::createFromData( $wc, $params );
            $rootCauldron->addCauldron( $siteCauldron );
            $siteCauldron->save();
        }
 
        if( !$siteCauldron ){
            return false;
        }
        elseif( !$structure ){
            return $siteCauldron;
        }

        $structureCauldron = false;
        foreach( $siteCauldron->children as $child ){
            if( $child->name === $structure )
            {
                $structureCauldron = $child;
                break;
            }
        }
    
        if( !$structureCauldron )
        {
            $params = [
                'name'      =>  $structure,
                'recipe'    =>  "folder",
                //'data'  =>  json_encode([ "structure" => "folder" ]),
            ];

            $structureCauldron = self::createFromData( $wc, $params );
            $siteCauldron->addCauldron( $structureCauldron );
            $structureCauldron->save();
        }
 
        if( !$structureCauldron ){
            return false;
        }
 
        return $structureCauldron;
    }   
}