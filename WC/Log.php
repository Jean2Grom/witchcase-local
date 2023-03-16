<?php
namespace WC;

use WC\Request;

class Log 
{
    const MAXARCHIVEFILES   = 100;
    const FATALERRORMESSAGE = "System down\nPlease contact administrator";
    const LOGFILENAME       = 'log/log.txt';
    
    /** 
     * maxsize of one log file before split
     * @var integer 
     */
    var $maxLog;
    var $logFilename;
    var $currentIP;
    var $errorLogFP;
    var $backtraceFileBegin;
    
    /** 
     * container
     * @var WitchCase 
     */
    var $wc;
    
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
            $this->wc->debug->enabled = true;
        }
        
        $this->errorLogFP = fopen( $this->logFilename, 'a' );
    }
    
    function debug( $variable, $userPrefix=false, $depth=5 )
    {
        return $this->wc->debug->dump( $variable, $userPrefix, $depth );
    }
    
    function error( $message, $fatal=false )
    {
        $userprefix = "ERROR : ";
        
        if( $fatal ){
            $userprefix = "FATAL ".$userprefix; 
        }
        
        $this->debug( $message."\n", $userprefix, 0, true );
        
        fwrite( $this->errorLogFP, $this->prefix().$message."\n" );
        
        if( $fatal ){
            $this->wc->debug->display();
            die(self::FATALERRORMESSAGE);
        }
        
        return;
    }
    /**
     * 
     * @param array $caller : [ 'file' => File full path name, 'line' => int line number of caller file ]
     * @return string
     */
    function prefix( $caller=null )
    {
        $prefix = "[ ".date(DATE_RFC2822)." ] [ ".$this->currentIP." ] ";
        
        if( !isset( $this->backtraceFileBegin ) ){   
            if( !filter_has_var(INPUT_SERVER, "DOCUMENT_ROOT") ){
                $this->backtraceFileBegin = 0;  
            }
            else {
                $this->backtraceFileBegin = strlen( filter_input(INPUT_SERVER, "DOCUMENT_ROOT") ) + 1;
            }   
        }
        
        if( !$caller )
        {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            foreach( $backtrace as $i => $backtraceData )
            {
                if( $backtraceData['class'] == "WC\Log"
                    || ($backtraceData['class'] == "WC\Debug" && $backtraceData['function'] == "prefix") 
                    || (    ($backtraceData['class'] == "WC\Debug" && $backtraceData['function'] == "dump") 
                            && !empty($backtrace[ $i+1 ]['class'])
                            && !empty($backtrace[ $i+1 ]['function'])
                            && $backtrace[ $i+1 ]['class'] == "WC\WitchCase" 
                            && ($backtrace[ $i+1 ]['function'] == "debug" || $backtrace[ $i+1 ]['function'] == "dump")  )
                ){
                    continue;
                }

                if( !empty($backtraceData['file']) )
                {
                    $caller = $backtraceData;
                    break;
                }
            }
        }
        
        $file = $caller['file'];
        
        if( str_starts_with($file, getcwd().'/') ){
            $file = substr( $file, strlen(getcwd())+1 );
        }
        
        $prefix .= "[ ".substr( $file, $this->backtraceFileBegin );
        $prefix .= " on line ".$caller["line"]." ] ";
        
        return $prefix;
    }
}
