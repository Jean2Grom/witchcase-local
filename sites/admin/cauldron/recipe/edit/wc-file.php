<?php /** @var WC\Cauldron $this */ 

$storagePath    = $this->content('storage-path')?->value() ?? "";
if( $storagePath )
{
    $storagePath = $this->wc->configuration->storage().'/'.$storagePath; 
    
    if( !is_file($storagePath) ){
        $storagePath = "";
    }
}

$filename       = $this->content('filename')?->value() ?? "";

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/wc-file.php" );