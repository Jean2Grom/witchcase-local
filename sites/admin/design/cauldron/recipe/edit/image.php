<?php
/** 
 * @var WC\Cauldron $this 
 * @var string $input 
 */

$this->wc->website->context->addJsFile('cauldron/image-edit.js');

$key = "_FILES__".md5( microtime().rand() );
?>

<div class="image-edit-container">
    <?php if ($this->exist()): ?>
        <input  type="hidden"
                name="<?=$input ?>[ID]"
                value="<?=$this->id ?>" />
    <?php endif; ?>
    <input  type="hidden"
            name="<?=$input ?>[type]"
            value="image" />

    <input  type="hidden"
            name="<?=$input.'[content][0][name]' ?>"
            value="name" />
    <input  type="hidden"
            name="<?=$input.'[content][0][type]' ?>"
            value="string" />
    <legend>Image name</legend>
    <input  class="image-input name"
            type="text"
            name="<?=$input.'[content][0][value]' ?>"
            value="<?=$name ?>"
            placeholder="enter image name" />
    
    <?php if( $this->content('file')->exist() ): ?>
        <input  type="hidden"
                name="<?=$input ?>[content][1][ID]"
                value="<?= $this->content('file')->id ?>" />
    <?php endif; ?>
    <input  type="hidden"
            name="<?=$input.'[content][1][name]' ?>"
            value="file" />
    <input  type="hidden"
            name="<?=$input.'[content][1][type]' ?>"
            value="wc-file" />    
    <div class="image-display" <?=$storagePath? '': 'style="display: none;"' ?>>
        <?php if( $storagePath ): ?>
            <legend class="current-image-focus">Current image</legend>
            <img class="current-image-focus" src="<?='/'.$storagePath ?>" /> 

            <input  type="text" 
                    class="current-image-focus"
                    name="<?=$input.'[content][1][content][0][value]'?>" 
                    value="<?=$storagePath ?>"  />            
        <?php endif; ?>

        <img class="new-image-focus" src="" />
        <a class="remove-image">
            <i class="fa fa-times"></i>
        </a>
    </div>
    
    <div class="file-input" <?= $storagePath ? 'style="display: none;"' : '' ?>>
        <input  type="hidden"
                name="<?=$input.'[content][1][content][0][name]' ?>"
                value="storage-path" />
        <input  type="hidden"
                name="<?=$input.'[content][1][content][0][type]' ?>"
                value="string" />
        <input  type="hidden"
                name="<?=$input.'[content][1][content][0][$_FILES]' ?>"
                value="<?=$key ?>" />

        <input  type="hidden"
                name="<?=$input.'[content][1][content][1][name]' ?>"
                value="filename" />
        <input  type="hidden"
                name="<?=$input.'[content][1][content][1][type]' ?>"
                value="string" />

        <?php if( $this->content('file')->content('storage-path')?->exist() ): ?>
            <input  type="hidden"
                    name="<?=$input.'[content][1][content][0][ID]' ?>"
                    value="<?=$this->content('file')->content('storage-path')?->id ?>" />
        <?php endif; ?>
        <?php if( $this->content('file')->content('filename')?->exist() ): ?>
            <input  type="hidden"
                    name="<?=$input.'[content][1][content][1][ID]' ?>"
                    value="<?=$this->content('file')->content('filename')?->id ?>" />
        <?php endif; ?>
        
        <legend>Upload file</legend>
        <input  class="upload-image-input"
                type="file"
                accept="image/*"
                name="<?= $key ?>" />

        <input  class="filename-image-input"
                type="hidden"
                name="<?=$input.'[content][1][content][1][value]' ?>"
                value="<?=$filename ?>" />
    </div>
    
    <input  type="hidden"
            name="<?=$input.'[content][2][name]' ?>"
            value="caption" />
    <input  type="hidden"
            name="<?=$input.'[content][2][type]' ?>"
            value="string" />
    <legend>Image caption</legend>
    <input  class="image-input caption"
            type="text"
            name="<?=$input.'[content][2][value]' ?>"
            value="<?=$caption ?>"
            placeholder="enter caption" />
</div>