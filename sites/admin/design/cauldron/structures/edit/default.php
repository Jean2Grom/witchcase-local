<?php /** @var WC\Cauldron $this */ ?>

<div class="fieldsets-container">
    <?php foreach( $this->content() as $content ): ?>
        <fieldset class="<?=$content->isStructure()? 'structure': 'ingredient'?>">
            <legend>
                <?php $structuredName = $this->editPrefix.'|'.$content->getInputName(false); ?>
                <span   class="span-input-toggle" 
                        data-name="<?=$structuredName?>-name"><?=$content->name ?></span>
                <input  class="span-input-toggle" 
                        type="text" 
                        name="<?=$structuredName?>-name" 
                        value="<?=$content->name ?>" />
                [<?=$content->type?>]
                <a class="up-fieldset">[&#8593;]</a>
                <a class="down-fieldset">[&#8595;]</a>
                <a class="remove-fieldset">[x]</a>
            </legend>
            <?php $content->edit( null, $this->getInputName(false) ) ?>
        </fieldset>
    <?php endforeach; ?>
</div>
