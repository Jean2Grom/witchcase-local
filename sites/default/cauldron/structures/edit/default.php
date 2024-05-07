<?php /** @var WC\Cauldron $this */ 

$designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/".$this->type.".php" );

if( !$designFile ){
    $designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/default.php" );
}

$callArray[] = "s#".$this->data->structure."#".$this->name;

if( $designFile ){
    include $designFile;
}
