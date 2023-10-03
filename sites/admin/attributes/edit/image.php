<?php
$this->wc->website->context->addJsFile('attribute/image-edit.js');

$srcFile = $this->getImageFile(); 

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/image.php");
