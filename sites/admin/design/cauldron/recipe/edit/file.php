<?php 
/** 
 * @var WC\Cauldron $this 
 * @var string $input 
 */

$this->wc->website->context->addJsFile('cauldron/file-edit.js');
$this->wc->website->context->addCssFile('cauldron/file-edit.css');
?>

<div>
    <legend>Filename</legend>
    <?php if ($this->content('filename')->exist()): ?>
        <input  
            name="<?=$input ?>[content][filename][ID]"
            value="<?=$this->content('filename')->id ?>"
            type="hidden"
        />
    <?php endif; ?>    
    <input  type="hidden" 
            name="<?=$input.'[content][filename][name]'?>" 
            value="filename" />
    <input  type="hidden" 
            name="<?=$input.'[content][filename][type]'?>" 
            value="string" />
    <input  class="file-input filename"
            type="text" 
            name="<?=$input.'[content][filename][value]'?>"
            value="<?=$title?>" 
            placeholder="enter filename"/>
    
    <?php if ($this->content('file')->exist()): ?>
        <input  type="hidden"
                name="<?=$input ?>[content][file][ID]"
                value="<?=$this->content('file')->id ?>" />
    <?php endif; ?>
    <input  type="hidden" 
            name="<?=$input.'[content][file][name]'?>" 
            value="file" />
    <input  type="hidden" 
            name="<?=$input.'[content][file][type]'?>" 
            value="wc-file" />
    <?php $this->content('file')->edit( 
            null, 
            [ 'input' => $input.'[content][file]' ]
        ); ?>
</div>
