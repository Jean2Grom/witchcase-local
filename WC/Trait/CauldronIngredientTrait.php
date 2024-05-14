<?php
namespace WC\Trait;

trait CauldronIngredientTrait
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

            $suffix = " (...)";
            if(  $maxChars && strlen($result) > $maxChars && strlen($suffix) < $maxChars )
            {
                $truncated  = substr(  $result, 0, ($maxChars-strlen( $suffix )) );
                $lastSpace  = strrpos( $truncated, " " );

                echo $lastSpace? substr($truncated, 0, $lastSpace): $truncated;
                echo $suffix;
            }
            else {
                echo $result;
            }
        }

        return;
    }


    function edit( ?string $filename=null, ?string $callerPrefix=null )
    {
        if( $callerPrefix ){
            $this->editPrefix = str_replace(' ', '-', $callerPrefix)."|".$this->editPrefix;
        }

        if( !$filename ){
            $filename = strtolower( $this->type );
        }
        
        $instanciedClass    = (new \ReflectionClass($this))->getName();
        $file               = $this->wc->website->getFilePath( $instanciedClass::DIR."/edit/".$filename.'.php');
        
        if( !$file ){
            $file = $this->wc->website->getFilePath( $instanciedClass::DIR."/edit/default.php");
        }
        
        if( $file ){
            include $file;
        }
        
        return;
    }

    function getInputName( bool $addBrackets=true ): string 
    {
        $identifier = $this->type."#".$this->getStringIdentitifer();
        //$suffix     = $addBrackets? str_repeat("[]", substr_count($this->editPrefix, "|")): "";
        $suffix     = $addBrackets? "[]": "";
        return $this->editPrefix."#".$identifier.$suffix;
    }

    function getStringIdentitifer(): string {
        return str_replace( ' ', '-', $this->name.($this->id ?? "") );
    }

    function splitInputName( string $inputName ): array
    {
        $return         = [];
        $inputsArray    = explode( "|", $inputName );

        foreach( $inputsArray as $inputTrunk )
        {
            $buffer     = explode( '#', $inputTrunk );
            $return[]   = [
                'class' => $buffer[0],
                'type'  => $buffer[1],
                'name'  => $buffer[2],
            ];
        }

        return $return;
    }
}
