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
            if( $this->isIngredient() && $maxChars && strlen($result) > $maxChars && strlen($suffix) < $maxChars )
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


    function edit( ?string $filename=null, ?array $params=null, ?string $callerPrefix=null )
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
        
        if( !$file ){
            return;
        }
        
        foreach( $params ?? [] as $name => $value ){
            $$name = $value;
        }

        include $file;

        return;
    }

    function getInputName( bool $addBrackets=true ): string 
    {
        $inputName  =   $this->editPrefix;
        $inputName  .=  "#".$this->type;
        $inputName  .=  "#".$this->getInputIdentifier();

        return $inputName;
    }

    function isIngredient(): bool {
        return $this->editPrefix === "i";
    }

    function isCauldron(): bool {
        return !$this->isIngredient();
    }

    function isStructure(): bool {
        return $this->isCauldron();
    }
}
