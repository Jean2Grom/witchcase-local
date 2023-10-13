<?php
$this->wc->website->context->addCssFile('attribute/image-view.css');

$srcFile = $this->getFile(); 

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/view/image.php" );
