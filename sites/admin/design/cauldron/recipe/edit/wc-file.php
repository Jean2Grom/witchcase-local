<?php 
/** 
 * @var WC\Cauldron $this 
 * @var string $input 
 */

$this->wc->website->context->addJsFile('cauldron/wc-file-edit.js');
$this->wc->website->context->addCssFile('cauldron/file-edit.css');

$key = "_FILES__".md5(microtime().rand());
?>

<div class="file-edit-container">
    <?php if( $this->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input?>[ID]" 
                value="<?=$this->id ?>" />
    <?php endif; ?>
    <input  type="hidden" 
                name="<?=$input?>[type]" 
                value="wc-file" />

    <div class="file-display" <?=$storagePath? '': 'style="display: none;"' ?>>
        <?php if( $storagePath ): ?>
            <a  class="current-file-focus" 
                href="/<?=$storagePath?>" 
                target="_blank">
                <?=$filename? $filename: basename($storagePath)?>
            </a>
        <?php endif; ?>
        
        <span class="new-file-focus"></span>

        <a class="remove-file">
            <i class="fa fa-times"></i>
        </a>
    </div>

    <div class="file-input" <?=$storagePath? 'style="display: none;"': '' ?>>
        <input  type="hidden" 
                name="<?=$input.'[content][0][name]'?>" 
                value="storage-path" />
        <input  type="hidden" 
                name="<?=$input.'[content][0][type]'?>" 
                value="string" />
        <input  type="hidden" 
                name="<?=$input.'[content][0][$_FILES]'?>" 
                value="<?=$key?>" />

        <input  type="hidden" 
                name="<?=$input.'[content][1][name]'?>" 
                value="filename" />
        <input  type="hidden" 
                name="<?=$input.'[content][1][type]'?>" 
                value="string" />

        <?php if( $this->content('storage-path')?->exist() ): ?>
            <input  type="hidden" 
                    name="<?=$input.'[content][0][ID]'?>" 
                    value="<?=$this->content('storage-path')?->id ?>" />
        <?php endif; ?>
        <?php if( $this->content('filename')?->exist() ): ?>
            <input  type="hidden" 
                    name="<?=$input.'[content][1][ID]'?>" 
                    value="<?=$this->content('filename')?->id ?>" />
        <?php endif; ?>

        <div class="switch-file-input-type">
            <a  <?=$this->content('storage-path')?->value()? '': 'class="selected"' ?> 
                data-target="upload-file-input">
                Upload file
            </a>
            /
            <a  class="" 
                data-target="move-file-input">
                Move server file
            </a>
        </div>
        <input  class="upload-file-input"
                <?=$this->content('storage-path')?->value()? 'style="display: none;"': '' ?>
                type="file" 
                name="<?=$key?>" />
        <input  class="move-file-input"
                <?=$this->content('storage-path')?->value()? '': 'style="display: none;"' ?>
                type="text" 
                name="<?=$input.'[content][0][value]'?>" 
                value="<?=$storagePath ?>" 
                placeholder="enter here full path filename" />

        <input  class="filename-file-input"
                type="hidden" 
                name="<?=$input.'[content][1][value]'?>"
                value="<?=$filename?>" />
    </div>
</div>
