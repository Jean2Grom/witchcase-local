<?php /** @var WC\Cauldron $this */ 

$this->wc->website->context->addJsFile('cauldron/file-edit.js');
$this->wc->website->context->addCssFile('cauldron/file-edit.css');

$path = $this->content('path')?->value() ?? "";
if( $path )
{
    $dir        = $this->wc->configuration->storage();

    if( !is_file($dir.'/'.$path) ){
        $path = "";
    }
}

$title = $this->content('title')?->value() ?? "";

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/file.php");
