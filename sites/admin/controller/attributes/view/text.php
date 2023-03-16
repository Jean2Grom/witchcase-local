<?php

$maxLenght  = 255;
$indice     = strpos( substr($this->values['value'], $maxLenght), " " ) + $maxLenght;
$value      = substr($this->values['value'], 0, $indice);

if( strlen($this->values['value']) > strlen($value) ){
    $value .= " (...)";
}

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/view/text.php");
