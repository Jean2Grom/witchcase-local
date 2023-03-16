<?php

$maxLenght  = 255;

$indice = strpos( substr($this->values['text'], $maxLenght), " " ) + $maxLenght;
$text   = substr($this->values['text'], 0, $indice);
if( strlen($this->values['text']) > strlen($text) ){
    $text .= " (...)";
}

$indice = strpos( substr($this->values['href'], $maxLenght), " " ) + $maxLenght;
$href   = substr($this->values['href'], 0, $indice);
if( strlen($this->values['href']) > strlen($href) ){
    $href .= " (...)";
}

if( $this->values['external'] ){
    $externalDisplay = "Ouverture en fenêtre externe";
}
else {
    $externalDisplay = "Ouverture dans la même fenêtre";
}

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/view/link.php" );
