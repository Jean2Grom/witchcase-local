<?php /** @var WC\Attribute\ImageAttribute $this */ 

$this->wc->website->context->addCssFile('attribute/image-view.css');

$srcFile = $this->getFile(); 

include $this->wc->website->getFilePath( self::VIEW_DIR."/view/image.php" );
