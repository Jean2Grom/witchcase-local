<?php
$this->wc->website->context->addJsFile('attribute/file-edit.js');
$this->wc->website->context->addCssFile('attribute/file-edit.css');

$srcFile = $this->getFile(); 

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/file.php");
