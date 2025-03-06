<?php /** @var WC\Cauldron $this */ 

$this->wc->website->context->addJsFile('cauldron/image-edit.js');
$this->wc->website->context->addCssFile('cauldron/file-edit.css');

$name      = $this->content('name')?->value() ?? "";
$caption    = $this->content('caption')?->value() ?? "";

$storagePath    = $this->content('file')?->content('storage-path')?->value() ?? "";
if( $storagePath )
{
    $storagePath = $this->wc->configuration->storage().'/'.$storagePath; 
    
    if( !is_file($storagePath) ){
        $storagePath = "";
    }
}

$filename       = $this->content('file')?->content('filename')?->value() ?? "";

include $this->wc->website->getFilePath( self::VIEW_DIR."/edit/image.php");
