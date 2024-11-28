<?php
namespace WC;

use WC\Request;

/**
 * Class handeling Log files and process
 * 
 * @author Jean2Grom
 */
class Log 
{
    const MAXARCHIVEFILES   = 100;
    const FATALERRORMESSAGE = "System down\nPlease contact administrator";
    const LOGFILENAME       = 'log/log.txt';
    
    /** 
     * maxsize of one log file before split
     * @var integer 
     */
    public $maxLog;
    public $logFilename;
    public $currentIP;
    public $errorLogFP;
    
    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;
    
    function __construct( WitchCase $wc, $logFilename=false )
    {
        $this->wc               =   $wc;
        $systemConfiguration    =   $this->wc->configuration->read('system');
        $this->maxLog           =   $systemConfiguration["maxLog"];
        $this->currentIP        =   Request::getRequesterIpAddress();
        
        if( $logFilename ){
            $this->logFilename = $logFilename;  
        }
        else {
            $this->logFilename = self::LOGFILENAME; 
        }
        
        if( !is_dir(dirname($this->logFilename)) ){
            mkdir( dirname($this->logFilename),  octdec($this->wc->configuration->read( 'system', 'createFolderRights' )), true );
        }
        
        // If log file is too big, renaming it
        if(     is_file($this->logFilename)
            &&  filesize($this->logFilename) > $this->maxLog 
        ){
            unlink( $this->logFilename.'.'.self::MAXARCHIVEFILES );
            
            for( $i=self::MAXARCHIVEFILES-1; $i>0; $i-- ){
                if( file_exists($this->logFilename.'.'.$i) )
                {
                    $oldFilename = $this->logFilename.'.'.$i;
                    $newFilename = $this->logFilename.'.'.($i+1);
                    rename( $oldFilename, $newFilename ); 
                }
            }
            
            rename( $this->logFilename, $this->logFilename.'.1' );
        }
        
        // Setting File pointers
        if(     isset($systemConfiguration['debug']) 
            &&  in_array( $this->currentIP, $systemConfiguration['debug'] )
        ){
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
            $this->wc->debug->enable();
        }
        
        $this->errorLogFP = fopen( $this->logFilename, 'a' );
    }
    
    function debug( $variable, $userPrefix=false, $depth=5, $callerArray=null )
    {
        return $this->wc->debug->dump( $variable, $userPrefix, $depth, $callerArray );
    }
    
    function error( $message, $fatal=false, $callerArray=null )
    {
        $userprefix = "ERROR : ";
        
        if( $fatal ){
            $userprefix = "FATAL ".$userprefix; 
        }
        
        $this->debug( $message."\n", $userprefix, 0, $callerArray );
        
        fwrite( $this->errorLogFP, $this->prefix().$message."\n" );
        
        if( $fatal )
        {
            $this->wc->debug->display();
            die(self::FATALERRORMESSAGE);
        }
        
        return;
    }
    /**
     * 
     * @param array|null $caller : [ 'file' => File full path name, 'line' => int line number of caller file ]
     * @param bool $addDateTimeIp
     * @return string
     */
    function prefix( ?array $caller=null, bool $addDateTimeIp=true ): string
    {
        
        $prefix = "";
        if( $addDateTimeIp ){
            $prefix .= "[ ".date(DATE_RFC2822)." ] [ ".$this->currentIP." ] ";
        }
        
        if( !$caller )
        {
            foreach( debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $backtraceData )
            {
                $class      = $backtraceData['class'] ?? false;
                $function   = $backtraceData['function'] ?? false;

                if( !in_array($function, ["call_user_func", "call_user_func_array", "__call"]) ){
                    if( !$class ){
                        break;
                    }
                    elseif(
                        !($class === __CLASS__ && $function === "prefix")
                        && !($class === "WC\\Debug" && $function === "prefix")
                        && !($class === "WC\\Debug" && $function === "dump")
                        && !($class === "WC\\WitchCase" && $function === "debug")
                        && !($class === "WC\\WitchCase" && $function === "dump")
                    ){
                        break;
                    }
                }
                
                $caller = $backtraceData;
            }
        }

        $file = $caller['file'] ?? false;
        $line = $caller['line'] ?? false;

        if( $file )
        {
            $prefix .= "[ ";
            if( str_starts_with($file, getcwd().'/') ){
                $prefix .= substr( $file, strlen(getcwd())+1 );
            }
            else {
                $prefix .= $file;
            }

            if( $line ){
                $prefix .= " on line ".$line;
            }            
            $prefix .= " ] ";
        }
        
        return $prefix;
    }
}
