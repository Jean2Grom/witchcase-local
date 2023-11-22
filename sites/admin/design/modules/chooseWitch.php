<?php /** @var WC\Module $this */

$this->addJsLibFile('jquery-3.6.0.min.js');
$this->addCssFile('choose-witch.css');
$this->addJsFile('choose-witch.js');
?>
<div id="choose-witch">
    <h3>
        <span>Choose Witch</span>
        <a class="close"><i class="fa fa-times"></i></a>
    </h3>
    
    <?php include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER.'/arborescence.php' ); ?>
</div>

