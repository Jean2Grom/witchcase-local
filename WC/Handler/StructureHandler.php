<?php 
namespace WC\Handler;

use WC\WitchCase;
use WC\Cauldron\Structure;
use WC\Ingredient;

class StructureHandler
{

    /**
     * Cauldron factory class, implements Cauldron with data provided
     * @param WitchCase $wc
     * @param array $data
     * @return Structure
     */
    static function createFromData(  WitchCase $wc, array $data ): Structure
    {
        $structure      = new Structure();
        $structure->wc  = $wc;
        
        $structure->properties = $data;

        self::readProperties( $structure );

        return $structure;
    }  

    /**
     * @param WitchCase $wc
     * @param string $file
     * @return ?Structure
     */
    static function createFromFile(  WitchCase $wc, string $file ): ?Structure
    {
        if( !is_file($file) ){
            return null;
        }

        $jsonString = file_get_contents($file);
        if( !$jsonString ){
            return null;
        }

        $jsonData   = json_decode($jsonString, true);
        if( !$jsonData ){
            return null;
        }
        
        $structure          = self::createFromData( $wc, $jsonData );
        $structure->file    = $file;
        
        return $structure;
    }  

    /**
     * Update  Object current state based on var "properties" (directly rad from JSON file) 
     * @return void
     */
    static function readProperties( Structure $structure ): void
    {
        $structure->name        = $structure->properties['name'] ?? null;
        $structure->composition = $structure->properties['composition'] ?? null;
        $structure->require     = $structure->properties['require'] ?? null;

        return;
    }


    /**
     * Update var "properties" (directly rad from JSON file) based on Object current state 
     * @return void
     */
    static function writeProperties( Structure $structure ): void
    {
        $structure->properties = [];
        if( $structure->name ){
            $structure->properties['name'] = $structure->name;
        }
        if( $structure->require ){
            $structure->properties['require'] = $structure->require;
        }
        if( $structure->composition )
        {
            $structure->properties['composition'] = [];
            foreach( $structure->composition as $item )
            {
                $content = [];
                if( !empty($item[ "mandatory" ]) ){
                    $content["mandatory"] = $item["mandatory"];
                }
                if( !empty($item[ "name" ]) ){
                    $content["name"] = $item["name"];
                }
                if( !empty($item[ "type" ]) ){
                    $content["type"] = $item["type"];
                }
                if( !empty($item[ "require" ]) ){
                    $content["require"] = $item["require"];
                }

                $structure->properties['composition'][] = $content;
            }
        }

        return;
    }


    static function resolve( array $structures ): bool
    {
        $return = true;
        foreach( $structures as $structure )
        {
            // if( $structure->type !== Structure::DEFAULT_TYPE ){
            //     if( !isset($structures[ $structure->type ]) ){
            //         $return = false;
            //     }
            //     else {
            //         $structure->structure =  $structures[ $structure->type ];
            //     }
            // }

            foreach(  $structure->composition ?? [] as $key => $content )
            {
                if( !isset($content['type']) || in_array($content['type'] ?? "", Ingredient::list()) ){
                    continue;
                }
                // elseif( $content['type'] === Structure::DEFAULT_TYPE ){
                //     $structure->composition[ $key ]['structure'] = self::createFromData( $structure->wc, $content );
                // }
                elseif( isset($structures[ $content['type'] ]) ){
                    $structure->composition[ $key ]['structure'] = $structures[ $content['type'] ] ;
                }
                else {
                    $return = false;
                }
            }
        }

        return $return;
    }

}