<?php

$this->module->addJsFile('externalTableAttributeEdit.js');
?>

<div id="<?=$this->type.'__'.$this->name?>_container">
    <? foreach( $values as $i => $value ) { ?>
    
        <? if( $value['file'] ) { ?>
            <p>
                <h2>Fichier actuel</h2>
                <a href="<?=$value['file']?>" target="_blank">
                    <?=basename($value['file'])?>
                </a>
                <input  type="submit"
                        name="storeButton"
                        class="deleteFile"
                        style=" background:url(<?=$this->module->getImageFile('disconnect.png')?>) no-repeat;
                                width: 16px;
                                height: 16px;
                                border: none;
                                margin-left: 10px;
                                font-size: 0;"
                        value="@_<?=$this->type.'#filedelete__'.$this->name?>[<?=$i?>]" />
            </p>
        <? } ?>

        <p>
            <h2>Sélectionner fichier</h2>

            <input  type="file" 
                    name="@_<?=$this->type.'#fileuploads__'.$this->name?>[<?=$i?>]" />
        </p>

        <p>
            <h2>Texte du lien</h2>
            <input  type="text" 
                    name="@_<?=$this->type.'#titles__'.$this->name?>[<?=$i?>]"
                    value="<?=$value['title']?>" />
        </p>
    <? } ?>
</div>

<input type="button"
       id="addElement_<?=$this->type.'__'.$this->name?>"
       onclick="addElementForm('<?=$this->type.'__'.$this->name?>')"
       value="Ajouter un fichier" />


<div id="<?=$this->type.'__'.$this->name?>_model" style="display: none;">
    <p>
        <h2>Sélectionner fichier</h2>

        <input  type="file" 
                name="@_<?=$this->type.'#fileuploads__'.$this->name?>[<?=count($values)?>]" />
    </p>
    
    <p>
        <h2>Texte du lien</h2>
        <input  type="text" 
                name="@_<?=$this->type.'#titles__'.$this->name?>[<?=count($values)?>]"
                value="" />
    </p>
    
    <input  type="submit"
            name="storeButton"
            value="Valider le nouveau fichier" />

</div>