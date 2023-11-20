<?php
namespace WC;

class Cache 
{    
    const DEFAULT_DIRECTORY     = "cache";
    const DEFAULT_DIR_RIGHTS    = "755";    // read/execute for all, write limited to self
    const DEFAULT_DURATION      = 86400;    // 24h
    const DEFAULT_UNIT          = "s";    // 24h
    
    private string $dir;
    var $createFolderRights;
    var $defaultUnit;
    var $defaultDuration;
    var $folders = [];
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc )
    {
        $this->wc = $wc;
        
        $this->createFolderRights   = $this->wc->configuration->read('system','createFolderRights') ?? self::DEFAULT_DIR_RIGHTS;
        $this->dir                  = $this->wc->configuration->read('cache','directory') ?? self::DEFAULT_DIRECTORY;
        $this->defaultUnit          = $this->wc->configuration->read('cache','durationUnit') ?? self::DEFAULT_UNIT;
        $this->defaultDuration      = self::getDuration($this->wc->configuration->read('cache','duration') ?? self::DEFAULT_DURATION, $this->defaultUnit);
        
        foreach( $this->wc->configuration->read('cache','folders') as $cacheConf => $cacheData ){
            $this->folders[ $cacheConf ] = [
                'directory' =>  $cacheData['directory'] ?? $cacheConf,
                'duration'  =>  self::getDuration($cacheData['duration'] ?? $this->defaultDuration, $cacheData['durationUnit'] ?? $this->defaultUnit),
            ];
        }
    }
    
    private static function getDuration( mixed $value, string $unit )
    {
        if( $value === '*' ){
            return '*';
        }
        
        switch( $unit )
        {
            case "weeks":
            case "week":
            case "w":
                $multiplier = 604800;
            break;
            
            case "days":
            case "day":
            case "d":
                $multiplier = 86400;
            break;
            
            case "hours":
            case "hour":
            case "H":
            case "h":
                $multiplier = 3600;
            break;
            
            case "minutes":
            case "minute":
            case "min":
            case "i":
                $multiplier = 60;
            break;
            
            case "s":
            default :
                $multiplier = 1;
            break;
        }
        
        return floor( $value * $multiplier );
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
    
    function reset(): bool
    {
        if( !is_dir($this->dir) ){
            return false;            
        }
        
        return $this->deleteFolder( $this->dir );
    }
    
    private function deleteFolder( string $folder  ): bool
    {
        if( !is_dir($folder) ){
            return false;
        }
        
        $files = array_diff( scandir($folder), ['.','..'] );
        
        foreach( $files as $file ){
            (is_dir("$folder/$file")) ? $this->deleteFolder("$folder/$file") : unlink("$folder/$file");
        }
        
        return rmdir($folder);
    }

}
