<?php if( $srcFile ): ?>
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
                value="<?=$this->name.'@'.$this->type.'#filedelete'?>" />
    </p>
<?php endif; ?>
    
<p>
    <h2>Sélectionner fichier</h2>
    
    <input  type="file" 
            name="<?=$this->name.'@'.$this->type.'#fileupload'?>" />
</p>

<p>
    <h2>Texte du lien</h2>
    <input  type="text" 
            name="<?=$this->name.'@'.$this->type.'#text'?>"
            value="<?=$this->values['text']?>" />
</p>