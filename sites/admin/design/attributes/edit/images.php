<?php

$this->module->addJsFile('externalTableAttributeEdit.js');
?>

<div id="<?=$this->type.'__'.$this->name?>_container">
    <? foreach( $values as $i => $value ) { ?>
    
        <? if( $value['file'] ) { ?>
            <p>
                <h2>Fichier image actuel</h2>

                <img src="<?=$value['file']?>" 
                     height="100" />
                <input  type="submit"
                        name="storeButton"
                        class="deleteImage"
                        style=" background:url(<?=$this->module->getImageFile('disconnect.png')?>) no-repeat;
                                width: 16px;
                                height: 16px;
                                border: none;
                                font-size: 0;"
                        value="@_<?=$this->type.'#filedelete__'.$this->name?>[<?=$i?>]" />
            </p>
        <? } ?>

        <p>
            <h2>Sélectionner fichier image</h2>

            <input  type="file" 
                    name="@_<?=$this->type.'#fileuploads__'.$this->name?>[<?=$i?>]" />
        </p>

        <p>
            <h2>Légende de l'image</h2>
            <input  type="text" 
                    name="@_<?=$this->type.'#titles__'.$this->name?>[<?=$i?>]"
                    value="<?=$value['title']?>" />
        </p>

        <p>
            <h2>cible du lien (url)</h2>
            <input  type="text" 
                    name="@_<?=$this->type.'#links__'.$this->name?>[<?=$i?>]"
                    value="<?=$value['link']?>" />
        </p>
    <? } ?>
</div>

<input  type="button"
        id="addElement_<?=$this->type.'__'.$this->name?>"
        onclick="addElementForm('<?=$this->type.'__'.$this->name?>')"
        value="Ajouter une image" />


<div id="<?=$this->type.'__'.$this->name?>_model" style="display: none;">
    <p>
        <h2>Sélectionner fichier image</h2>

        <input  type="file" 
                name="@_<?=$this->type.'#fileuploads__'.$this->name?>[<?=count($values)?>]" />
    </p>
    
    <p>
        <h2>Légende de l'image</h2>
        <input  type="text" 
                name="@_<?=$this->type.'#titles__'.$this->name?>[<?=count($values)?>]"
                value="" />
    </p>

    <p>
        <h2>cible du lien (url)</h2>
        <input  type="text" 
                name="@_<?=$this->type.'#links__'.$this->name?>[<?=count($values)?>]"
                value="" />
    </p>
    
    <input  type="submit"
            name="storeButton"
            value="Valider la nouvelle image" />

</div>