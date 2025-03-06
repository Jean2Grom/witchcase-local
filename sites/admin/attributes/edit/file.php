<?php /** @var WC\Attribute\FileAttribute $this */ 

$this->wc->website->context->addJsFile('attribute/file-edit.js');
$this->wc->website->context->addCssFile('attribute/file-edit.css');

$srcFile = $this->getFile(); 

include $this->wc->website->getFilePath( self::VIEW_DIR."/edit/file.php");
