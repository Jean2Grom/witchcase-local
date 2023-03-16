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
                value="@_<?=$this->type.'#filedelete__'.$this->name?>" />
    </div>
<?php endif; ?>
    
<p>
    <h2>Sélectionner fichier image</h2>
    
    <input  type="file" 
            class="changeImage"
            name="@_<?=$this->type.'#fileupload__'.$this->name?>" />
    
    <input  type="hidden" 
            name="@_<?=$this->type.'#file__'.$this->name?>" 
            id="@_<?=$this->type.'#file__'.$this->name?>" 
            value="<?=$this->values['file']?>" />
</p>

<p>
    <h2>Légende de l'image</h2>
    <input  type="text" 
            name="@_<?=$this->type.'#title__'.$this->name?>"
            value="<?=$this->values['title']?>" />
</p>

