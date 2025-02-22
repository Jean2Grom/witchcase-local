<?php 
/** 
 * @var WC\Cauldron $this 
 * @var string $input 
 */

$this->wc->website->context->addJsFile('cauldron/file-edit.js');
$this->wc->website->context->addCssFile('cauldron/file-edit.css');
?>

<div>
    <?php if( $this->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input?>[ID]" 
                value="<?=$this->id ?>" />
    <?php endif; ?>
    <input  type="hidden" 
            name="<?=$input?>[type]" 
            value="file" />

    <?php if ($this->content('filename')->exist()): ?>
        <input  type="hidden"
                name="<?=$input ?>[content][0][ID]"
                value="<?=$this->content('filename')->id ?>" />
    <?php endif; ?>
    <input  type="hidden" 
            name="<?=$input.'[content][0][name]'?>" 
            value="filename" />
    <input  type="hidden" 
            name="<?=$input.'[content][0][type]'?>" 
            value="string" />
    <legend>Filename</legend>
    <input  class="file-input filename"
            type="text" 
            name="<?=$input.'[content][0][value]'?>"
            value="<?=$title?>" 
            placeholder="enter filename"/>
            
    <?php if ($this->content('file')->exist()): ?>
        <input  type="hidden"
                name="<?=$input ?>[content][1][ID]"
                value="<?=$this->content('file')->id ?>" />
    <?php endif; ?>
    <input  type="hidden" 
            name="<?=$input.'[content][1][name]'?>" 
            value="file" />
    <?php $this->content('file')->edit( 
            null, 
            [ 'input' => $input.'[content][1]' ]
        ); ?>
</div>
