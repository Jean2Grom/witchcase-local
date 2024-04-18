<?php 
namespace WC\Handler;

use WC\WitchCase;
use WC\Witch;
use WC\DataAccess\Witch as DataAccess;
use WC\Datatype\ExtendedDateTime;

class WitchHandler
{
    const MAX_INT_ID_LENGTH = 10;

    /**
     * Witch factory class, implements witch whith data provided
     * @param WitchCase $wc
     * @param array $data
     * @return Witch
     */
    static function createFromData(  WitchCase $wc, array $data ): Witch
    {
        $witch      = new Witch();
        $witch->wc  = $wc;
        
        foreach( Witch::FIELDS as $field ){
            $witch->properties[ $field ] = NULL;
        }

        $witch->properties = $data;
        
        self::readProperties( $witch );

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
    static function readProperties( Witch $witch ): void
    {
        if( isset($witch->properties['id']) ){
            $witch->id = (int) $witch->properties['id'];
        }
        
        if( isset($witch->properties['name']) ){
            $witch->name = $witch->properties['name'];
        }
        
        if( isset($witch->properties['datetime']) ){
            $witch->datetime = new ExtendedDateTime($witch->properties['datetime']);
        }
        
        if( isset($witch->properties['site']) ){
            $witch->site = $witch->properties['site'];
        }
        
        if( isset($witch->properties['status']) ){
            $witch->statusLevel = (int) $witch->properties['status'];
        }
        
        $witch->status = null;
        
        return;
    }

    /**
     * Witch factory class, reads witch data associated whith id
     * @param WitchCase $wc
     * @param int $id   witch id to create
     * @return mixed implemented Witch object, boolean false if data not found
     */
    static function createFromId( WitchCase $wc, int $id ): mixed
    {
        $data = DataAccess::readFromId($wc, $id);
        
        if( empty($data) ){
            return false;
        }
        
        return self::createFromData( $wc, $data );
    }



    /**
     * Usefull for string standardisation (urls, names)
     * @param string $string
     * @return string
     */
    static function cleanupString( string $string ): string
    {
        $characters =   array(
                'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 
                'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
                'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 
                'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
                'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 
                'í' => 'i', 'î' => 'i', 'ï' => 'i',
                'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 
                'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
                'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 
                'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
                'Œ' => 'oe', 'œ' => 'oe',
                '$' => 's'  );
        
        $string0    = strtr($string, $characters);
        $string1    = preg_replace('#[^A-Za-z0-9]+#', '-', $string0);
        $string2    = trim($string1, '-');
        
        return strtolower($string2);
    }
    
    /**
     * Usefull for cleaning up url strings
     * @param string $urlRaw
     * @return string
     */
    static function urlCleanupString( string $urlRaw ): string
    {
        $url    = "";
        $buffer = explode('/', $urlRaw);
        foreach( $buffer as $bufferElement )
        {
            $prefix = substr($bufferElement, 0, 1) == '-'? '-': '';
            $suffix = substr($bufferElement, -1) == '-'? '-': '';
            
            $urlPart = $prefix.self::cleanupString( $bufferElement ).$suffix;
            if( !empty($url) ){
                $url .= "/";
            }
            if( !empty($bufferElement) ){
                $url .= $urlPart;
            }
        }
        
        return $url;
    }

    /**
     * Reorder a witch array based on priority
     * @param array $witchesList
     * @return array
     */
    static function reorderWitches( array $witchesList ): array
    {
        $orderedWitchesIds = [];
        $refMaxPossiblrPriority = 1;
        for( $i=1; $i <= self::MAX_INT_ID_LENGTH; $i++ ){
            $refMaxPossiblrPriority = $refMaxPossiblrPriority*10;
        }

        foreach( $witchesList as $witchItem ) 
        {
            $priority = $refMaxPossiblrPriority - $witchItem->priority;
            
            for( $i=strlen($priority); $i < self::MAX_INT_ID_LENGTH; $i++  ){
                $priority = "0".$priority;
            }
            
            $orderIndex = $priority."__".mb_strtolower($witchItem->name)."__".$witchItem->id;
            $orderedWitchesIds[ $orderIndex ] = $witchItem->id;
        }
        
        ksort($orderedWitchesIds);
        
        $orderedWitches = [];
        foreach( $orderedWitchesIds as $orderedWitchId ){
            $orderedWitches[ $orderedWitchId ] = $witchesList[ $orderedWitchId ];
        }
        
        return $orderedWitches;
    }

    /**
     * 
     */
    static function recursiveTree( Witch $witch, $sitesRestrictions=false, $currentId=false, $maxStatus=false, ?array $hrefCallBack=null )
    {
        if( !is_null($witch->site) 
            && is_array($sitesRestrictions)
            && !in_array($witch->site, $sitesRestrictions) ){
            return false;
        }

        $path       = false;
        if( $currentId && $currentId == $witch->id ){
            $path = true;
        }
        
        $daughters  = [];
        if( $witch->id ){
            foreach( $witch->daughters() as $daughterWitch )
            {
                if( $maxStatus !== false && $daughterWitch->statusLevel > $maxStatus ){
                    continue;
                }

                $subTree        = self::recursiveTree( $daughterWitch, $sitesRestrictions, $currentId, $maxStatus, $hrefCallBack );
                if( $subTree === false ){
                    continue;
                }

                if( $subTree['path'] ){
                    $path = true;
                }

                $daughters[ $subTree['id'] ]    = $subTree;
            }
        }

        $tree   = [ 
            'id'                => $witch->id,
            'name'              => $witch->name,
            'site'              => $witch->site ?? "",
            'description'       => $witch->data,
            'craft'             => $witch->hasCraft(),
            'invoke'            => $witch->hasInvoke(),
            'daughters'         => $daughters,
            'daughters_orders'  => array_keys( $daughters ),
            'path'              => $path,
        ];
        
        if( $hrefCallBack ){
            $tree['href'] = call_user_func( $hrefCallBack, $witch );
        }
        
        return $tree;
    }



    /**
     * Mother witch manipulation
     * @param Witch $descendant
     * @param Witch $mother
     */
    static function setMother(  Witch $descendant, Witch $mother )
    {
        self::unsetMother( $descendant );

        $descendant->mother = $mother;
        if( !in_array($descendant->id, array_keys($mother->daughters ?? [])) ){
            $mother->addDaughter($descendant);
        }
        
        return;
    }
    
    /**
     * Mother witch manipulation
     * @param Witch $witch
     */
    static function unsetMother( Witch $witch )
    {
        if( !empty($witch->mother) && !empty($witch->mother->daughters[ $witch->id ]) ){
            unset($witch->mother->daughters[ $witch->id ]);
        }
        
        $witch->mother = null;
        
        return;
    }
        


}