<?php
namespace WC;

class Cache 
{    
    const DEFAULT_DIRECTORY     = "cache";
    const DEFAULT_DIR_RIGHTS    = "755";    // read/execute for all, write limited to self
    const DEFAULT_DURATION      = 86400;    // 24h
    
    var string $dir;
    var $createFolderRights;
    var int $defaultDuration;
    var $folders = [];
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc )
    {
        $this->wc = $wc;
        
        $this->createFolderRights   = $this->wc->configuration->read('system','createFolderRights') ?? self::DEFAULT_DIR_RIGHTS;
        $this->dir                  = $this->wc->configuration->read('cache','directory') ?? self::DEFAULT_DIRECTORY;
        $this->defaultDuration      = $this->wc->configuration->read('cache','duration') ?? self::DEFAULT_DURATION;
        
        foreach( $this->wc->configuration->read('cache','folders') as $cacheConf => $cacheData )
        {
            $this->folders[ $cacheConf ] = [
                'directory' =>  $cacheData['directory'] ?? $cacheConf,
                'duration'  =>  $cacheData['duration'] ?? $this->defaultDuration,
            ];
        }
    }
    
    function read( string $folder, string $filebasename ): mixed
    {
        $cached     = null;    
        $cacheFile  = $this->get( $folder, $filebasename );
        
        if( $cacheFile ){
            include $cacheFile;
        }
        
        return $cached;
    }    
    
    function get( string $folder, string $filebasename ): mixed
    {
        $cacheFolder = $this->dir.'/';
        
        if( !isset($this->folders[ $folder ]) )
        {
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
    
    function delete( string $folder, string $filebasename ): bool
    {
        $cacheFolder = $this->dir.'/';
        
        if( !isset($this->folders[ $folder ]) )
        {
            $cacheFolder    .=  $folder;
            $cacheDuration  =   $this->defaultDuration;
        }
        else 
        {
            $cacheFolder    .=  $this->folders[ $folder ]['directory'];
            $cacheDuration  =   $this->folders[ $folder ]['duration'];
        }
        
        if( !is_dir($cacheFolder) ){
            $this->wc->log->error("Trying to delete ressource under uncreated folder : ".$folder);
            return false;
        }
        
        $filename = $cacheFolder.'/'.$filebasename.".php";
        
        if( file_exists($filename) ){
            unlink($filename);
        }
        
        return true;
    }
    
    function create( string $folder, string $filebasename, mixed $value ): mixed
    {
        $cacheFolder = $this->dir.'/';
        
        if( !isset($this->folders[ $folder ]) ){
            $cacheFolder    .=  $folder;
        }
        else {
            $cacheFolder    .=  $this->folders[ $folder ]['directory'];
        }
        
        if( !is_dir($cacheFolder) 
            && !mkdir($cacheFolder,  octdec( $this->createFolderRights ), true)
        ){
            $this->wc->log->error("Can't create cache folder : ".$folder);
            return false;
        }
                
        // Writing cache policies files (based on profile)
        $filename = $cacheFolder."/".$filebasename.".php";
        
        if( file_exists($filename) ){
            unlink($filename);
        }
        
        $cacheFileFP = fopen( $filename, 'a');
        fwrite($cacheFileFP, "<?php\n");
        fwrite($cacheFileFP, "$"."cached = ");
        
        ob_start();
        var_export($value);
        $buffer = ob_get_contents();
        ob_end_clean();
        
        fwrite($cacheFileFP, $buffer);
        fwrite($cacheFileFP, ";\n");
        
        fclose($cacheFileFP);

        return $filename;
    }    
}
