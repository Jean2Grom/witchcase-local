<?php

?>

<h2>cible du lien (url)</h2>
<p>
    <input  type="text" 
            name="@_<?=$this->type.'#href__'.$this->name?>"
            value="<?=$this->values['href']?>" />
</p>

<h2>texte du lien</h2>
<p>
    <input  type="text" 
            name="@_<?=$this->type.'#text__'.$this->name?>"
            value="<?=$this->values['text']?>" />
</p>

<p>
    <input  type="checkbox" 
            <? if($this->values['external']) { ?>
                checked
            <? } ?>
            name="@_<?=$this->type.'#external__'.$this->name?>" 
            value="1" />
    Ouvrir dans une nouvelle fenêtre (ou onglet)
</p>
