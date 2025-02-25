<?php
namespace WC\Cauldron;

use WC\Cauldron;
use WC\Configuration;
use WC\WitchCase;
use WC\Handler\RecipeHandler as Handler;
use WC\Cauldron\Ingredient;

class Recipe 
{
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

        if( !$this->wc->configuration->createFolder( dirname($file) ) )
        {
            $this->wc->log->error( "Can't create Recipe folder : ".dirname($file) );
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

        $priorityInterval   = 100;
        $priority           = count($this->composition ?? []) * $priorityInterval;

        foreach( $this->composition ?? [] as $i => $contentData )
        {
            $contentName                        =   $contentData['name'] ?? $i;
            $contentInitProperties              =   $initProperties[ $contentName ] ?? $contentData['init'] ?? [];
            $contentInitProperties['priority']  =   $priority;
            $priority                           -=  $priorityInterval;
            $cauldron->create( 
                $contentName,  
                $contentData['type'] ?? "folder",
                $contentInitProperties
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

    function min(): ?int {
        return $this->require['min'] ?? null;
    }

    function max(): ?int {
        return $this->require['max'] ?? null;
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


    function isCompositionElement( string $type, string $name ): bool
    {
        foreach( $this->composition ?? [] as $element ){
            if( $type === $element['type'] && $name === $element['name'] ){
                return true;
            }
        }

        return false;
    }
}