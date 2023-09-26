<?php if( $srcFile ): ?>
    <div class="current-image-display">
        <h2>Current image</h2>
        
        <img src="<?=$srcFile?>" 
             height="150" />
        
        <a class="deleteImage" value="<?=$this->name.'@'.$this->type.'#filedelete'?>" >
            <i class="fa fa-times"></i>
        </a>
    </div>
<?php endif; ?>
    
<p>
    <h2>Browse image file</h2>
    
    <input  type="file" 
            class="changeImage"
            name="<?=$this->name.'@'.$this->type.'#fileupload'?>" />
    
    <input  type="hidden" 
            name="<?=$this->name.'@'.$this->type.'#file'?>" 
            id="<?=$this->name.'@'.$this->type.'#file'?>" 
            value="<?=$this->values['file']?>" />
</p>

<p>
    <h2>Caption</h2>
    <input  type="text" 
            name="<?=$this->name.'@'.$this->type.'#title'?>"
            value="<?=$this->values['title']?>" />
</p>

