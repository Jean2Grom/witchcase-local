<?php /** @var WC\Cauldron $this */ 

$this->wc->website->context->addJsFile('cauldron/wc-file-edit.js');
//$this->wc->website->context->addCssFile('cauldron/file-edit.css');

$storagePath    = $this->content('storage-path')?->value() ?? "";
if( $storagePath )
{
    $dir        = $this->wc->configuration->storage();

    if( !is_file($dir.'/'.$storagePath) ){
        $storagePath= "";
    }
}

$filename       = $this->content('filename')?->value() ?? "";

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/wc-file.php");
