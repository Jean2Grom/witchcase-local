<?php
namespace WC;

class Cache 
{    
    const CACHEDIR          = "cache";
    
    var $createFolderRights = "755";    // read/execute for all, write limited to self
    var $defaultDuration    = 86400;    // 24h
    var $folders            = [];
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc )
    {
        $this->wc = $wc;
        
        $this->createFolderRights   = $this->wc->configuration->read( 'system', 'createFolderRights' ) ?? $this->createFolderRights;
        $this->defaultDuration      = $this->wc->configuration->read( 'cache', 'duration' ) ?? $this->defaultDuration;
        
        foreach( $this->wc->configuration->read( 'cache', 'folders' ) as $cacheConf => $cacheData )
        {
            $this->folders[ $cacheConf ] = [
                'directory' =>  $cacheData['directory'] ?? $cacheConf,
                'duration'  =>  $cacheData['duration'] ?? $this->defaultDuration,
            ];
        }
    }
    
    function get( $folder, $filebasename )
    {
        $cacheFolder = self::CACHEDIR.'/';
        
        if( !isset($folder, $this->folders) )
        {
            $this->wc->log->debug("Trying to access unmanaged cache folder : ".$folder);
            $cacheFolder    .=  $folder;
            $cacheDuration  =   $this->defaultDuration;
        }
        else 
        {
            $cacheFolder    .=  $this->folders[ $folder ]['directory'];
            $cacheDuration  =   $this->folders[ $folder ]['duration'];
        }
        
        if( !is_dir($cacheFolder) 
            && !mkdir($cacheFolder,  octdec( $this->createFolderRights ), true)
        ){
            $this->wc->log->error("Can't create cache folder : ".$folder);
            return false;
        }
        
        $filename = $cacheFolder.'/'.$filebasename.".php";
        
        if( file_exists($filename) )
        {
            if( $cacheDuration == '*' 
                || (time() - filemtime($filename)) < (int) $cacheDuration ){
                return $filename;
            }
            
            unlink($filename);  
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
        $cacheFolder = self::CACHEDIR.'/';
        
        if( !isset($folder, $this->folders) )
        {
            $this->wc->log->debug("Trying to access unmanaged cache folder : ".$folder);
            $cacheFolder    .=  $folder;
            $cacheDuration  =   $this->defaultDuration;
        }
        else 
        {
            $cacheFolder    .=  $this->folders[ $folder ]['directory'];
            $cacheDuration  =   $this->folders[ $folder ]['duration'];
        }
        
        if( !is_dir($cacheFolder) ){
            $this->wc->log->error("Trying to delete uncreated folder : ".$folder);
            return false;
        }
        
        $filename = $cacheFolder.'/'.$filebasename.".php";
        
        if( file_exists($filename) ){
            unlink($filename);
        }
        
        return true;
    }
    
    function create( $folder, $filebasename, $value, $varname=false )
    {
        $cacheFolder = self::CACHEDIR.'/';
        
        if( !isset($folder, $this->folders) )
        {
            $this->wc->log->debug("Trying to access unmanaged cache folder : ".$folder);
            $cacheFolder    .=  $folder;
            $cacheDuration  =   $this->defaultDuration;
        }
        else 
        {
            $cacheFolder    .=  $this->folders[ $folder ]['directory'];
            $cacheDuration  =   $this->folders[ $folder ]['duration'];
        }
        
        if( !is_dir($cacheFolder) 
            && !mkdir($cacheFolder,  octdec( $this->createFolderRights ), true)
        ){
            $this->wc->log->error("Can't create cache folder : ".$folder);
            return false;
        }
        
        if( $varname == false ){
            $varname = $filebasename;
        }
        
        // Writing cache policies files (based on profile)
        $filename = $cacheFolder."/".$filebasename.".php";
        
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
