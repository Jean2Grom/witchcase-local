<?php
$this->wc->website->context->addJsFile('attribute/file-edit.js');

$srcFile = $this->getFile(); 

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/file.php");
