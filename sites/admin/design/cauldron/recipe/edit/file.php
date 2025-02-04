<?php 
/** 
 * @var WC\Cauldron $this 
 * @var string $input 
 */

$this->wc->website->context->addJsFile('cauldron/file-edit.js');
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

    <input  type="hidden" 
            name="<?=$input.'[content][0][name]'?>" 
            value="filename" />
    <input  type="hidden" 
            name="<?=$input.'[content][0][type]'?>" 
            value="string" />

    <input  type="hidden" 
            name="<?=$input.'[content][1][name]'?>" 
            value="file" />
    <input  type="hidden" 
            name="<?=$input.'[content][1][type]'?>" 
            value="wc-file" />

    <legend>Filename</legend>
    <input  class="file-input filename"
            type="text" 
            name="<?=$input.'[content][0][value]'?>"
            value="<?=$title?>" 
            placeholder="enter filename"/>
            
    <?php $this->content('file')->edit( 
            null, 
            [ 'input' => $input.'[content][1]' ]
        ); ?>
</div>

