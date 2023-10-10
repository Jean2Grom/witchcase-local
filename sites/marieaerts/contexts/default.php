<?php
$contextCraft = $this->wc->witch('home')->craft();


$metaTitle = $this->wc->witch('home')->name;
if( $contextCraft->attribute('meta-title')->content() ){
    $metaTitle = $contextCraft->attribute('meta-title')->content();
}
elseif( $contextCraft->attribute('title')->content() ){
    $metaTitle = $contextCraft->attribute('title')->content();
}

$this->addCssFile('styles.css');
$this->view();