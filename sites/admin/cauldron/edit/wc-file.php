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

if( !isset($input) ){
    $input = "content";
}
else {
    $input .= "[content]";
}

include $this->wc->website->getFilePath( self::VIEW_DIR."/edit/wc-file.php" );