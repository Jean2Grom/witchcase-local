<?php /** @var WC\Cauldron $this */ 

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

include $this->wc->website->getFilePath( self::VIEW_DIR."/edit/file.php");
