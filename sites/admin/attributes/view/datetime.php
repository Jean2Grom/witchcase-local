<?php

$value      =  $this->content();
$inputValue =  "UNDEFINED";

if( !empty($value) ){
    $inputValue = $value->format( 'd/m/Y H:i:s' );
}

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/view/datetime.php");