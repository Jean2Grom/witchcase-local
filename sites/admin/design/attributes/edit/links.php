<?php

$this->module->addJsFile('externalTableAttributeEdit.js');
?>

<div id="<?=$this->type.'__'.$this->name?>_container">
    <? foreach( $values as $i => $value ) { ?>
        <h2>cible du lien (url)</h2>
        <p>
            <input  type="text" 
                    name="@_<?=$this->type.'#hrefs__'.$this->name?>[<?=$i?>]"
                    value="<?=$value['href']?>" />
        </p>
        <h2>texte du lien</h2>
        <p>
            <input  type="text" 
                    name="@_<?=$this->type.'#texts__'.$this->name?>[<?=$i?>]"
                    value="<?=$value['text']?>" />
        </p>
        <p>
            <input  type="checkbox" 
                    <? if($value['external']) { ?>
                        checked
                    <? } ?>
                    name="@_<?=$this->type.'#externals__'.$this->name?>[<?=$i?>][]" 
                    value="1" />
            Ouvrir dans une nouvelle fenêtre (ou onglet)
        </p>
    <? } ?>
</div>

<input  type="button"
        id="addElement_<?=$this->type.'__'.$this->name?>"
        onclick="addElementForm('<?=$this->type.'__'.$this->name?>')"
        value="Ajouter un lien" />
<br/>

<div id="<?=$this->type.'__'.$this->name?>_model" style="display: none;">
    <h2>cible du lien (url)</h2>
    <p>
        <input  type="text" 
                name="@_<?=$this->type.'#hrefs__'.$this->name?>[<?=count($values)?>]"
                value="" />
    </p>
    <h2>texte du lien</h2>
    <p>
        <input  type="text" 
                name="@_<?=$this->type.'#texts__'.$this->name?>[<?=count($values)?>]"
                value="" />
    </p>
    <p>
        <input  type="checkbox" 
                checked
                name="@_<?=$this->type.'#externals__'.$this->name?>[<?=count($values)?>][]" 
                value="1" />
        Ouvrir dans une nouvelle fenêtre (ou onglet)
    </p>
    
    <input  type="submit"
            name="storeButton"
            value="Valider le nouveau lien" />
</div>