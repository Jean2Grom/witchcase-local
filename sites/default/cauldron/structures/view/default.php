<?php /** @var WC\Cauldron $this */ 

echo "yyy";

$designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/view/".$this->type.".php");

if( !$designFile ){
    $designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/view/default.php");
}

if( $designFile ){
    include $designFile;
}
