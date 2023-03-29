
<h2>cible du lien (url)</h2>
<p>
    <input  type="text" 
            name="<?=$this->name.'@'.$this->type.'#href'?>"
            value="<?=$this->values['href']?>" />
</p>

<h2>texte du lien</h2>
<p>
    <input  type="text" 
            name="<?=$this->name.'@'.$this->type.'#text'?>"
            value="<?=$this->values['text']?>" />
</p>

<p>
    <input  type="checkbox" 
            <?php if($this->values['external']): ?>
                checked
            <?php endif; ?>
            name="<?=$this->name.'@'.$this->type.'#external'?>" 
            value="1" />
    Ouvrir dans une nouvelle fenêtre (ou onglet)
</p>
