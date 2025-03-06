<?php /** @var WC\Attribute $this */ 

$designFile = $this->wc->website->getFilePath( self::VIEW_DIR."/view/".$this->type.".php");

if( !$designFile ){
    $designFile = $this->wc->website->getFilePath( self::VIEW_DIR."/view/default.php");
}

if( $designFile ){
    include $designFile;
}

