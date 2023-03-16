<?php

?>

<p>
    <strong>type de contenu&nbsp;:</strong>
    <?=$this->parameters["structure"]["value"]?>
</p>

<p>
    <strong>emplacement&nbsp;:</strong>
    <? if( $localisationTarget ) { ?>
        [<?=$localisationTarget->site?>]
        <?=$localisationTarget->url?>
    <? } else { ?>
        Pas d'emplacement
    <? } ?>
</p>

<strong>contenu(s)&nbsp;:</strong>
<? if( count($names) > 0 ) { ?>
    <ul>
        <? foreach( $names as $name ) { ?>
            <li>
                - <?=$name?>
            </li>
        <? } ?>
    </ul>
<? } else { ?>
    Pas de contenus
<? } ?>