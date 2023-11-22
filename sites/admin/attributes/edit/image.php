<?php /** @var WC\Attribute\ImageAttribute $this */ 

$this->wc->website->context->addJsFile('attribute/image-edit.js');
$this->wc->website->context->addCssFile('attribute/image-edit.css');

$srcFile = $this->getFile(); 

include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER."/edit/image.php");
