<?php /** @var WC\Attribute\DatetimeAttribute $this */ 

$value      =  $this->content();
$inputValue =  "UNDEFINED";

if( !empty($value) ){
    $inputValue = $value->format( 'Y-m-d' ).'T'.$value->format( 'H:i' );
}

include $this->wc->website->getFilePath( self::VIEW_DIR."/edit/datetime.php");
