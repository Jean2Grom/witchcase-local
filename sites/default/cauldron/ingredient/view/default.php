<?php /** @var WC\Cauldron\Ingredient $this */ 

$designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/view/".$this->type.".php");

if( !$designFile ){
    $designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/view/default.php");
}

if( $designFile ){
    include $designFile;
}

