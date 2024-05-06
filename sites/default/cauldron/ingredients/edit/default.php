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

$this->wc->debug( $prefix, "ingredient" );
$this->wc->debug( $suffix, "ingredient-suffix" );


if( $designFile ){
    include $designFile;
}

