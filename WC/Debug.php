<?php
namespace WC;

/**
 * Debug class for developpers
 * Can display big var debugs, exec times, 
 * and can log in file if wanted
 */
class Debug 
{
    const CSS_STYLE = [
        'width'             => 'max-content',
        'overflow-y'        => 'auto',
        'padding'           => '7px 15px',
        'background-color'  => '#1a1a1a',
        'color'             => '#358a2e',
        'box-shadow'        => '3px 3px 5px #aaa',
        'max-height'        => '90%',
        'text-align'        => 'left',
        'text-transform'    => 'none',
        'position'          => 'absolute',
        'z-index'           => 99999999,
        'left'              => '2%',
        'top'               => '2%',
        'border-radius'     => '10px',
    ];
    
    const PHP_ERROR_LEVELS = [
        E_ERROR             => "E_ERROR",
        E_WARNING           => "E_WARNING",
        E_PARSE             => "E_PARSE",
        E_NOTICE            => "E_NOTICE",
        E_CORE_ERROR        => "E_CORE_ERROR",
        E_CORE_WARNING      => "E_CORE_WARNING",
        E_COMPILE_ERROR     => "E_COMPILE_ERROR",
        E_COMPILE_WARNING   => "E_COMPILE_WARNING",
        E_USER_ERROR        => "E_USER_ERROR",
        E_USER_WARNING      => "E_USER_WARNING",
        E_USER_NOTICE       => "E_USER_NOTICE",
        E_STRICT            => "E_STRICT",
        E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
        E_DEPRECATED        => "E_DEPRECATED",
        E_USER_DEPRECATED   => "E_USER_DEPRECATED",
        E_ALL               => "E_ALL"
    ];
    
    /**
     * To enable / disable the debug
     * 
     * @var bool
     */
    var $enabled;
    
    
    var $buffer = [];
    
    /**
     * Microtime value of implementation of class
     * Update it for exec times analysis
     * 
     * @var int
     */
    var $refMicroTime;
    
    /** 
     * container
     * @var WitchCase 
     */
    var $wc;
    
    /**
     * @param \WC\WitchCase $wc : container
     */
    function __construct( WitchCase $wc )
    {
        $this->wc           = $wc;
        $this->enabled      = false;
        $this->refMicroTime = microtime(true);
        
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            $errLevel = "PHP ERROR HANDLED: ";
            $errLevel .= self::PHP_ERROR_LEVELS[$errno] ?? '';
            
            $this->dump( $errstr, $errLevel, 1, ['file' => $errfile, 'line' => $errline] );
        });
    }
    
    /**
     * Print a variable dump formatted and processed if big variable
     * 
     * @param mixed $variable : variable to dump
     * @param string $userPrefix : prefix a header to the dump
     * @param int $depth : max depth to explore, ignored if set to 0 
     * @param array $callerArray : [ 'file' => File full path name, 'line' => int line number of caller file ]
     * @return void
     */
    function dump( $variable, $userPrefix='', $depth=10, $callerArray=null )
    {
        if( $this->enabled )
        {            
            ob_start();
            
            if( !empty($userPrefix) ){
                echo $userPrefix."\n";
            }
            
            if( !empty($this->prefix($callerArray)) ){
                echo $this->prefix($callerArray)."\n\n";
            }
            
            if( $depth == 0 ){
                var_dump($variable);
            }
            else {
                $this->debugPrint($variable, $depth);
            }
            
            $this->buffer[] = ob_get_contents();
            ob_end_clean();
        }
        
        return $this;
    }
    
    /**
     * Recursive function to print analysis
     * 
     * @param type $variable
     * @param type $depth
     * @param type $i
     * @param type $objects
     * @return type
     */
    private function debugPrint( $variable, $depth=10, $i=0, &$objects=array() )
    {
        $search     = array("\0", "\a", "\b", "\f", "\n", "\r", "\t", "\v");
        $replace    = array('\0', '\a', '\b', '\f', '\n', '\r', '\t', '\v');
        
        $string     = '';
        
        switch( gettype($variable) ) 
        {
            case 'boolean':
                $string .= $variable? 'true': 'false'; 
            break;
            
            case 'resource':
                $string .= '[resource]';
            break;
            
            case 'NULL':
                $string .= "null";
            break;
            
            case 'unknown type': 
                $string .= '???';
            break;
            
            case 'string':
                $len        = strlen( $variable );
                $variable   = str_replace( $search, $replace, $variable );
                
                $string .= '"'.$variable.'"';
            break;
            
            case 'array':
                $len = count( $variable );
                
                if( $i == $depth ){
                    $string .= 'array('.$len.') {...}';  
                }
                elseif( !$len ){
                    $string .= 'array(0) {}';
                }
                else 
                {
                    $keys   = array_keys( $variable );
                    $spaces = str_repeat( ' ', $i*2 );
                    
                    $string .= "array($len)\n".$spaces.'{';
                    
                    foreach( $keys as $key ) 
                    {
                        $string .=  "\n".$spaces."  [".( is_numeric($key)? $key : '"'.$key.'"' )."] => ";
                        $string .=  $this->debugPrint( $variable[$key], $depth, $i+1, $objects );
                    }
                    
                    $string .=  "\n".$spaces.'}';
                }
            break;
                
            case 'object':
                $id = array_search( $variable, $objects, true );
                
                if( $id!==false ){
                    $string .=  get_class($variable).'#'.( $id+1 ).' {...}'; 
                }
                elseif( $i==$depth ){
                    $string .=  get_class($variable).' {...}'; 
                }
                elseif( $i > 0 && get_class($variable) === "WC\\WitchCase" ){
                    $string .=  get_class($variable).' {...}'; 
                }
                else 
                {
                    $id     = array_push( $objects, $variable );
                    $array  = (array) $variable;
                    $spaces = str_repeat( ' ', $i*2 );
                    
                    $string .=  get_class($variable)."#$id\n".$spaces.'{';
                    
                    $properties = array_keys($array);
                    foreach( $properties as $property ) 
                    {
                        $name   = str_replace( "\0", ':', trim($property) );
                        
                        $string .= "\n".$spaces."  [\"".$name."\"] => ";
                        $string .= $this->debugPrint( $array[$property], $depth, $i+1, $objects );
                    }
                    
                    $string.= "\n".$spaces.'}';
                }
            break;
            
            default :
                $string .= $variable;
            break;
        }
        
        if( $i>0 ){
            return $string;
        }
        
        echo $string;
        
        return;
    }
    
    /**
     * 
     * usage :
     * $debug->time();
     * 
     * -> code to check execution time
     * 
     * $debug->time();
     * 
     * -> code to check execution time ...
     * 
     * $debug->time(); ... 
     * 
     * etc...
     * 
     * @param boolean $onlyInit for disable printTime
     * @return int microtime current value stored
     */
    public function time( $onlyInit=false )
    {
        $time = microtime(true);
        
        if( !$onlyInit && $this->refMicroTime )
        {
            $microSecDiff   =   $time - $this->refMicroTime;
            $secDiff        =   floor( $microSecDiff/1e+6 );
            $microSecDiff   -=  $secDiff * 1e+6;
            $mSecDiff       =   floor( $microSecDiff/1e+3 );
            $microSecDiff   -=  $mSecDiff * 1e+3;
            
            $this->dump( $secDiff." seconds, ".$mSecDiff." milliseconds and ".$microSecDiff." microseconds", "Time" );
        }
        
        $this->refMicroTime = $time;
        
        return $this->refMicroTime;
    }
    
    /**
     * Get the Log class prefix
     * 
     * @param array $callerArray : [ 'file' => File full path name, 'line' => int line number of caller file ]
     * 
     * @return string
     */
    function prefix($callerArray=null)
    {
        if( empty($this->wc->log) ){
            return '';
        }
        
        return $this->wc->log->prefix($callerArray);
    }
    
    
    function display(): void
    {
        if( $this->enabled && $this->buffer )
        {
            $style = self::CSS_STYLE;
            
            $styleAttribute = "";
            foreach( $style as $property => $value ){
                $styleAttribute .= $property.": ".$value."; "; 
            }
                        
            echo "<div id=\"wc-debug\" style=\"".$styleAttribute."\">";
            
            echo "<div style=\"color: red;position: fixed;cursor: pointer\" ";
            echo "onclick=\"document.getElementById('wc-debug').style.display = 'none';\">[X]</div>";
            
            echo "<pre style=\"margin-top: 25px;\">";
            foreach( $this->buffer as $buffer ){
                echo $buffer."\n\n\n";
            }
            echo "</pre></div>";
        }
    }
}
