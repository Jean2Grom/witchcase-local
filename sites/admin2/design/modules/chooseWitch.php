<?php 
    $this->addCssFile('choose-witch.css');
    $this->addJsFile('choose-witch.js');
?>
<div id="choose-witch">
    <h3>
        Choose Witch
        <a class="close"><i class="fa fa-times"></i></a>
    </h3>
    
    <?php include $this->wc->website->getFilePath( self::DESIGN_SUBFOLDER.'/arborescence.php' ); ?>
</div>

