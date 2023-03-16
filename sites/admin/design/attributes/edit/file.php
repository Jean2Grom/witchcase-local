<?php

?>

<? if( $srcFile ) { ?>
    <p>
        <h2>Fichier actuel</h2>
        <a href="<?=$srcFile?>" target="_blank">
            <?=$this->values['file']?>
        </a>
        <input  type="submit"
                name="storeButton"
                class="deleteImage"
                style=" background:url(<?=$this->module->getImageFile('disconnect.png')?>) no-repeat;
                        width: 16px;
                        height: 16px;
                        border: none;
                        margin-left: 10px;
                        font-size: 0;"
                value="@_<?=$this->type.'#filedelete__'.$this->name?>" />
    </p>
<? } ?>
    
<p>
    <h2>Sélectionner fichier</h2>
    
    <input  type="file" 
            name="@_<?=$this->type.'#fileupload__'.$this->name?>" />
</p>

<p>
    <h2>Texte du lien</h2>
    <input  type="text" 
            name="@_<?=$this->type.'#text__'.$this->name?>"
            value="<?=$this->values['text']?>" />
</p>