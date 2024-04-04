<?php
namespace WC\Traits;



trait DisplayTrait
{
    function display( ?string $filename=null, ?int $maxChars=null )
    {
        if( !$filename ){
            $filename = strtolower( $this->type );
        }
        
        $instanciedClass    = (new \ReflectionClass($this))->getName();
        $file               = $this->wc->website->getFilePath( $instanciedClass::DIR."/view/".$filename.'.php');
        
        if( !$file ){
            $file = $this->wc->website->getFilePath( $instanciedClass::DIR."/view/default.php");
        }
        
        if( $file )
        {
            ob_start();
            include $file;
            $result = ob_get_contents();
            ob_end_clean();

            if(  $maxChars && strlen($result) > $maxChars )
            {
                $truncated = substr(  $result, 0, ($maxChars-6) );
                $lastSpace = strrpos( $truncated, " " );
                echo $lastSpace? substr($truncated, 0, $lastSpace)." (...)": $truncated." (...)";
            }
            else {
                echo $result;
            }
        }


        return;
    }

}
