<?php

namespace WC;

class Cache {
    
    const CACHEDIR          = 'cache';
    
    var $createFolderRights = "755";
    var $folders            = [];
    var $cacheDurations     = [];
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc )
    {
        $this->wc = $wc;
        
        foreach( $this->wc->configuration->read( 'caches' ) as $cacheConf => $cacheData )
        {
            $this->folders[ $cacheConf ]                    = $cacheData['folder'];
            $this->cacheDurations[ $cacheData['folder'] ]   = $cacheData['duration'];
        }
        
        $this->createFolderRights   = $this->wc->configuration->read( 'system', 'createFolderRights' );
    }
    
    function get( $folder, $filebasename )
    {
        if( strstr($folder, "/") !== false ){   
            $cacheFolder = dirname($folder);
        }
        else{
            $cacheFolder = $folder; 
        }
        
        if( !in_array($cacheFolder, $this->folders) )
        {
            $this->wc->log->error("Trying to access unmanaged cache folder : ".$folder);
            return false;
        }
        
        if( !is_dir(self::CACHEDIR.'/'.$folder) 
            && !mkdir( self::CACHEDIR.'/'.$folder,  octdec($this->createFolderRights), true )
        ){
            $this->wc->log->error("Can't create cache folder : ".$folder);
            return false;
        }
        
        $filename = self::CACHEDIR.'/'.$folder.'/'.$filebasename.".php";
        
        if( file_exists($filename) )
        {
            $unlink = false;
            if( $this->cacheDurations[ $cacheFolder ] != '*' )
            {
                $limit = (int) $this->cacheDurations[ $cacheFolder ];
                
                if( (time() - filemtime($filename)) > $limit ){
                    $unlink = true;
                }
            }
            
            if( $unlink ){   
                unlink($filename);  
            }
            else {
                return $filename;
            }
        }
        
        $method = 'create'.ucfirst($folder).'File';
        
        if( in_array($method, get_class_methods( get_called_class() )) ){   
            return call_user_func( [ $this, $method ], $filebasename );    
        }
        else {
            return false;
        }
    }
    
    function delete( $folder, $filebasename )
    {
        if( strstr($folder, "/") !== false ){
            $cacheFolder = dirname($folder);
        }
        else {
            $cacheFolder = $folder;
        }
        
        if( !in_array($cacheFolder, $this->folders) )
        {
            $this->wc->log->error("Trying to delete unmanaged cache folder : ".$folder);
            return false;
        }
        
        $filename = self::CACHEDIR.'/'.$folder.'/'.$filebasename.".php";
        
        if( file_exists($filename) ){
            unlink($filename);
        }
        
        return true;
    }
    
    function create( $folder, $filebasename, $value, $varname=false )
    {
        if( strstr($folder, "/") !== false ){
            $cacheFolder = dirname($folder);
        }
        else {
            $cacheFolder = $folder;
        }
        
        if( !in_array($cacheFolder, $this->folders) )
        {
            $this->wc->log->error("Trying to delete unmanaged cache folder : ".$folder);
            return false;
        }
        
        if( $varname == false ){
            $varname = $filebasename;
        }
        
        // Writing cache policies files (based on profile)
        $filename = self::CACHEDIR."/".$folder."/".$filebasename.".php";
        
        if( file_exists($filename) ){
            unlink($filename);
        }
        
        $cacheFileFP = fopen( $filename, 'a');
        fwrite($cacheFileFP, "<?php\n");
        fwrite($cacheFileFP, "$".$varname." = ");
        
        ob_start();
        var_export($value);
        $buffer = ob_get_contents();
        ob_end_clean();
        
        fwrite($cacheFileFP, $buffer);
        fwrite($cacheFileFP, ";\n");
        
        return $filename;
    }    
}
