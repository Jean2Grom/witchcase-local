<?php /** @var WC\Ingredient $this */ 

$designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/".$this->type.".php");

if( !$designFile ){
    $designFile = $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/default.php");
}


$prefix = "";
$suffix = "";
foreach( $callArray as $caller )
{
    $prefix .= $caller."___";
    $suffix .= "[]";
}

if( $designFile ){
    include $designFile;
}

