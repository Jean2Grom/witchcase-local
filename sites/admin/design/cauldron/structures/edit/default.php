<?php 
/**
 * @var WC\Cauldron $this 
 * @var ?array $ingredients 
 * @var ?array $structures 
 * @var string $input
 */ 
?>

<?php if( $this->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input?>[ID]" value="<?=$this->id ?>" />
<?php endif; ?>

<div class="fieldsets-container">
    <?php if( empty($this->content()) ): ?>
        <input  type="hidden" 
                name="<?=$input?>[content]" value="" />

    <?php else: foreach( $this->content() as $content ): 
        $integrationCountClass  = substr_count($this->editPrefix, '|') % 4;
        $contentInput = $input."[content][".$content->getInputIndex()."][".$content->type."]";
        ?>
        <fieldset class="<?=$content->isIngredient()? 'ingredient': 'structure integration-'.$integrationCountClass?>">
            <legend>
                <span   class="span-input-toggle" 
                        data-input="<?=$contentInput?>[name]" 
                        data-value="<?=$content->name ?>"><?=$content->name ?></span>
                [<?=$content->type?>]
                <a class="up-fieldset">[&#8593;]</a>
                <a class="down-fieldset">[&#8595;]</a>
                <a class="remove-fieldset">[x]</a>
            </legend>
            <?php $content->edit( 
                null, 
                [
                    'ingredients'   => $ingredients, 
                    'structures'    => $structures,
                    'input'         => $contentInput,
                ]
            ); ?>
        </fieldset>
    <?php endforeach; endif; ?>

    <?php $this->wc->witch()?->modules['cauldron']?->include(
            'cauldron/add.php', 
            [ 
                'ingredients'   => $ingredients, 
                'structures'    => $structures, 
                'input'         => $input."[content][new]",
            ]
        );?>

</div>
