<?php /** @var WC\Attribute\FileAttribute $this */ 

$srcFile = $this->getFile(); 

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/view/file.php" );
