<?php /** @var WC\Attribute $this */ 

$designFile = $this->wc->website->getFilePath( self::VIEW_DIR."/edit/".$this->type.".php");

if( !$designFile ){
    $designFile = $this->wc->website->getFilePath( self::VIEW_DIR."/edit/default.php");
}

if( $designFile ){
    include $designFile;
}

