<?php

?>

<p>
    <? if( $srcFile ) { ?>
        <strong><?=$this->values['text']?></strong>
        =>
        <a href="<?=$srcFile?>" target="_blank">
            <?=$this->values['file']?>
        </a>
    <? } else { ?>
        Pas de fichier
    <? } ?>
</p>