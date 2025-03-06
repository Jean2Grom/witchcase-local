<?php /** @var WC\Cauldron\Ingredient $this */ 

$viewFile = $this->wc->website->getFilePath( self::VIEW_DIR."/view/".$this->type.".php");

if( !$viewFile ){
    $viewFile = $this->wc->website->getFilePath( self::VIEW_DIR."/view/default.php");
}

if( $viewFile ){
    include $viewFile;
}

