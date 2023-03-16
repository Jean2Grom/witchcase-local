<?php

?>

<div id="attributs">
    <h2>Attributs du contenu :</h2>
    <ul>
        <? foreach( $viewTarget->attributes as $attribute ){ ?>
            <li>
                <fieldset>
                    <legend><?=$attribute->name?> [<?=$attribute->type?>]</legend>
                    <?=$attribute->display()?>
                </fieldset>
            </li>
        <? } ?>
    </ul>
    <div class="break"></div>
</div>
<? if( $draftCount ) { ?>
    <div class="content-view-full">
        <div class="class-folder">
            <p>
                Il y a actuellement <?=$draftCount?> Brouillon(s) en cours
            </p>
        </div>
    </div>

<? } ?>

<div class="controlbar">
    <form action="" method="post" name="delete">
        <? if( $modificationHref ) { ?>
            <a href="<?=$modificationHref ?>">
                <input  type="button" 
                        title="Modifier le contenu de cet élément." 
                        value="Modifier" 
                        name="editButton" 
                        class="button" />
            </a>
        <? } ?>

        <? if( $publishButton ) { ?>
            <input  type="submit" 
                    title="<?=$publishButton?>" 
                    value="<?=$publishButton?>" 
                    name="publishButton" 
                    class="button" />
        <? } ?>

        <? if( $deleteButton ) { ?>
            <input  type="submit" 
                    title="<?=$deleteButton?>" 
                    value="<?=$deleteButton?>" 
                    name="deleteButton" 
                    class="button" />
        <? } ?>
    </form>
</div>