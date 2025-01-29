<?php 
/** 
 * @var WC\Cauldron $this 
 * @var string $input 
 */

$this->wc->debug( $input );

$key = "_FILES__".md5(microtime().rand());
?>

<div class="file-edit-container">

    <?php if( $this->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input?>[ID]" 
                value="<?=$this->id ?>" />
    <?php endif; ?>

    <div class="file-display" <?=$path? '': 'style="display: none;"' ?>>
        <?php if( $path ): ?>
            <h2 class="current-file-focus">
                Current file
            </h2>        
            <a  class="current-file-focus" 
                href="/<?=$path?>" 
                target="_blank">
                <?=$title? $title: basename($path)?>
            </a>
        <?php endif; ?>
        
        <span class="new-file-focus"></span>

        <a class="remove-file">
            <i class="fa fa-times"></i>
        </a>
    </div>

    <div class="file-input" <?=$path? 'style="display: none;"': '' ?>>
        <input  type="hidden" 
                name="<?=$input.'[content][0][name]'?>" 
                value="path" />
        <input  type="hidden" 
                name="<?=$input.'[content][0][type]'?>" 
                value="string" />
        <input  type="hidden" 
                name="<?=$input.'[content][0][$_FILES]'?>" 
                value="<?=$key?>" />

        <input  type="hidden" 
                name="<?=$input.'[content][1][name]'?>" 
                value="title" />
        <input  type="hidden" 
                name="<?=$input.'[content][1][type]'?>" 
                value="string" />

        <?php if( $this->content('path')?->exist() ): ?>
            <input  type="hidden" 
                    name="<?=$input.'[content][0][ID]'?>" 
                    value="<?=$this->content('path')?->id ?>" />
        <?php endif; ?>
        <?php if( $this->content('title')?->exist() ): ?>
            <input  type="hidden" 
                    name="<?=$input.'[content][1][ID]'?>" 
                    value="<?=$this->content('title')?->id ?>" />
        <?php endif; ?>

        <div class="switch-file-input-type">
            <a  <?=$this->content('path')?->value()? '': 'class="selected"' ?> 
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
                <?=$this->content('path')?->value()? 'style="display: none;"': '' ?>
                type="file" 
                name="<?=$key?>" />
        <input  class="move-file-input"
                <?=$this->content('path')?->value()? '': 'style="display: none;"' ?>
                type="text" 
                name="<?=$input.'[content][0][value]'?>" 
                value="<?=$this->content('path')?->value() ?>" 
                placeholder="enter here full path filename" />
    </div>

    <legend>Filename</legend>
    <input  class="name-file-input"
            type="text" 
            name="<?=$input.'[content][1][value]'?>"
            value="<?=$title?>" 
            placeholder="enter filename"/>


</div>


<style>
    .file-input, 
    .file-input > .switch-file-input-type {
        margin-bottom: 8px;
    }
    .file-input > .switch-file-input-type > .selected {
        cursor: initial;
        color: #ff9900;
    }
</style>

<hr/>

<div class="current-file-display" <?=$path? '': 'style="display: none;"' ?>>
    <?php if( $path ): ?>
        <h2 class="current-file-target">Current file</h2>
        
        <a class="current-file-target" 
           href="/<?=$path?>" 
           target="_blank">
            <?=$title? $title: basename($path)?>
        </a>
    <?php endif; ?>
    
    <span class="new-file-target"></span>
    <a class="delete-file">
        <i class="fa fa-times"></i>
    </a>
</div>

<div class="change-file-container" <?=$path? 'style="display: none;"': '' ?>>
    <input  type="hidden" name="<?=$input.'[content][0][name]'?>" value="path" />
    <input  type="hidden" name="<?=$input.'[content][0][type]'?>" value="string" />
    <input  type="hidden" name="<?=$input.'[content][0][$_FILES]'?>" value="<?=$key?>" />
    <?php if( $this->content('path')?->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input.'[content][0][ID]'?>" 
                value="<?=$this->content('path')?->id ?>" />
    <?php endif; ?>
    <input  type="hidden" name="<?=$input.'[content][1][name]'?>" value="title" />
    <input  type="hidden" name="<?=$input.'[content][1][type]'?>" value="string" />
    <?php if( $this->content('title')?->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input.'[content][1][ID]'?>" 
                value="<?=$this->content('title')?->id ?>" />
    <?php endif; ?>
    <div>
        <h2>Upload file</h2>
        <input  type="file" 
                class="change-file"
                name="<?=$key?>" />
    </div>
    <div>    
        <h2>Move server file</h2>
        <input  type="text" 
                class="change-file"
                name="<?=$input.'[content][0][value]'?>" 
                value="<?=$this->content('path')?->value() ?>" />
        <em>enter here full path filename</em>
    </div>
</div>

<p>
    <h2>Filename</h2>
    <input  type="text" 
            name="<?=$input.'[content][1][value]'?>"
            value="<?=$title?>" />
</p>
