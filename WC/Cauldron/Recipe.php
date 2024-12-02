<?php
namespace WC\Cauldron;

use WC\Cauldron;
use WC\Configuration;
use WC\WitchCase;
use WC\Handler\RecipeHandler as Handler;
use WC\Ingredient;

class Recipe 
{
    const DEFAULT_DIR_RIGHTS    = "755";    // read/execute for all, write limited to self

    public ?string $file = null;
    public ?string $name;

    public array $properties    = [];

    var ?array $require;
    var ?array $composition;

    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;


    /**
     * Name reading
     * @return string
     */
    public function __toString(): string {
        return $this->name;
    }

    /**
     * Save Recipe data into file
     * @param ?string $fileParam : provided filename, default null
     * @return bool
     */
    function save( ?string $fileParam=null ): bool
    {
        if( empty($this->name) ){
            return false;
        }

        Handler::writeProperties($this);

        $file = $fileParam ?? $this->file;

        if( !$file ){
            $file = Configuration::RECIPES_DIR."/".$this->name.".json";
        }
        elseif( substr($file, -5) !== ".json" ){
            $file .= ".json";
        }

        $dir =  dirname($file);
        $createFolderRights = $this->wc->configuration->read('system','createFolderRights') ?? self::DEFAULT_DIR_RIGHTS;

        if( !is_dir($dir) 
            && !mkdir($dir,  octdec( $createFolderRights ), true)
        ){
            $this->wc->log->error("Can't create Recipe folder : ".$dir);
            return false;
        }

        $backupFile = false;
        if( $this->file && $this->file !== $file ){
            $backupFile = $this->file;
        }
        elseif( file_exists($file) )
        {
            $backupFile = $file.".sav";
            rename( $file, $backupFile );
        }

        $cacheFileFP = fopen( $file, 'a' );
        fwrite( $cacheFileFP, json_encode($this->properties, JSON_PRETTY_PRINT) );            
        fclose( $cacheFileFP );

        if( $backupFile && !Handler::extractJsonDataFromFile($file) )
        {
            unlink( $file );
            rename( $backupFile, $file );
            return false;
        }

        if( $backupFile ){
            unlink( $backupFile );
        }

        return true;
    }


    function factory( ?string $name=null, array $initProperties=[] ): ?Cauldron
    {
        $cauldron           = new Cauldron();
        $cauldron->wc       = $this->wc;
        $cauldron->name     = $name ?? $this->name;
        $cauldron->recipe   = $this->name;

        foreach( $this->composition ?? [] as $i => $contentData ){
            $cauldron->create( 
                $contentData['name'] ?? $i,  
                $contentData['type'] ?? "folder",
                $initProperties['name'] ?? $contentData['init'] ?? []
            );
        }

        return $cauldron;
    }


    function isAllowed( string $testedType ): bool
    {
        if( isset($this->require['accept'])
            && !in_array($testedType, $this->require['accept']) 
        ){
            return false;
        }

        if( isset($this->require['refuse'])
            && in_array($testedType, $this->require['refuse']) 
        ){
            return false;
        }

        return true;
    }


    function allowedIngredients(): array
    {
        $ingredients = [];
        foreach( Ingredient::list() ?? [] as $ingredient ){
            if( $this->isAllowed($ingredient) ){
                $ingredients[] = $ingredient;
            }
        }

        return $ingredients;
    }

    
    function allowedRecipes(): array
    {
        $recipes = [];
        foreach( $this->wc->configuration->recipes() as $recipe ){
            if( $this->isAllowed($recipe->name) ){
                $recipes[] = $recipe;
            }
        }
        
        return $recipes;
    }

}