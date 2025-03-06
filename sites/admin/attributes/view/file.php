<?php /** @var WC\Attribute\FileAttribute $this */ 

$srcFile = $this->getFile(); 

include $this->wc->website->getFilePath( self::VIEW_DIR."/view/file.php" );
