<?php if( $srcFile ): ?>
    <div class="current-image-display">
        <h2>Fichier image actuel</h2>
        
        <img src="<?=$srcFile?>" 
             height="100" />
        <input  type="submit"
                class="deleteImage"
                style=" background:url(<?=$this->wc->website->context->getImageFile('disconnect.png')?>) no-repeat;
                        width: 16px;
                        height: 16px;
                        border: none;
                        font-size: 0;"
                value="<?=$this->name.'@'.$this->type.'#filedelete'?>" />
    </div>
<?php endif; ?>
    
<p>
    <h2>Sélectionner fichier image</h2>
    
    <input  type="file" 
            class="changeImage"
            name="<?=$this->name.'@'.$this->type.'#fileupload'?>" />
    
    <input  type="hidden" 
            name="<?=$this->name.'@'.$this->type.'#file'?>" 
            id="<?=$this->name.'@'.$this->type.'#file'?>" 
            value="<?=$this->values['file']?>" />
</p>

<p>
    <h2>Légende de l'image</h2>
    <input  type="text" 
            name="<?=$this->name.'@'.$this->type.'#title'?>"
            value="<?=$this->values['title']?>" />
</p>

