<?php /** @var WC\Cauldron $this */ 

$this->wc->website->context->addJsFile('cauldron/file-edit.js');
//$this->wc->website->context->addCssFile('cauldron/file-edit.css');


$title = $this->content('filename')?->value() ?? "";

$storagePath    = $this->content('file')?->content('storage-path')?->value() ?? "";
if( $storagePath )
{
    $storagePath = $this->wc->configuration->storage().'/'.$storagePath; 
    
    if( !is_file($storagePath) ){
        $storagePath = "";
    }
}

$filename       = $this->content('file')?->content('filename')?->value() ?? "";


include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/file.php");
