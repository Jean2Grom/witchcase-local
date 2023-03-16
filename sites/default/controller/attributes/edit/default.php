<?php
$designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/".$this->type.".php");

if( !$designFile ){
    $designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/default.php");
}

if( $designFile ){
    include $designFile;
}

