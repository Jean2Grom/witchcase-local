<?php
namespace WC\Cauldron;

use WC\Configuration;
use WC\WitchCase;
use WC\Handler\RecipeHandler as Handler;

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

}