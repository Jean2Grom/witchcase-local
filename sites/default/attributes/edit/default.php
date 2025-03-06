<?php /** @var WC\Attribute $this */ 

$viewFile = $this->wc->website->getFilePath( self::VIEW_DIR."/edit/".$this->type.".php");

if( !$viewFile ){
    $viewFile = $this->wc->website->getFilePath( self::VIEW_DIR."/edit/default.php");
}

if( $viewFile ){
    include $viewFile;
}

