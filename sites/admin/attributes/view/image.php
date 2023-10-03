<?php
$this->wc->website->context->addCssFile('attribute/image-view.css');

$srcFile = $this->getImageFile(); 

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/view/image.php" );
