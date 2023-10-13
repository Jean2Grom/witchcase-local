<?php if( $srcFile ): ?>
    <div class="current-file-display">
        <h2>Current file</h2>
        
        <a href="<?=$srcFile?>" target="_blank">
            <?=$this->values['file']?>
        </a>
        
        <a class="deleteFile" value="<?=$this->name.'@'.$this->type.'#filedelete'?>" >
            <i class="fa fa-times"></i>
        </a>
    </div>
<?php endif; ?>
   

<p>
    <h2>Browse file</h2>
    
    <input  type="file" 
            class="changeFile"
            name="<?=$this->name.'@'.$this->type.'#fileupload'?>" />
    
    <input  type="hidden" 
            name="<?=$this->name.'@'.$this->type.'#file'?>" 
            id="<?=$this->name.'@'.$this->type.'#file'?>" 
            value="<?=$this->values['file']?>" />
</p>

<p>
    <h2>Filename</h2>
    <input  type="text" 
            name="<?=$this->name.'@'.$this->type.'#title'?>"
            value="<?=$this->values['title']?>" />
</p>
