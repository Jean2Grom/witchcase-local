<?php 
/**
 * @var WC\Cauldron $this 
 * @var string $input
 */ 
?>

<?php if( $this->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input?>[ID]" value="<?=$this->id ?>" />
<?php endif; ?>

<div class="fieldsets-container">
    <?php if( empty($this->contents()) ): ?>
        <input  type="hidden" 
                name="<?=$input?>[content]" value="" />

    <?php else: foreach( $this->contents() as $content ): 
        $integrationCountClass  = substr_count($this->editPrefix, '[') % 4;
        $contentInput           = $input."[content][".$content->getInputIndex()."]";
        ?>
        <fieldset class="<?=$content->isIngredient()? 'ingredient': 'cauldron integration-'.$integrationCountClass?>">
            <legend>
                <span   class="span-input-toggle" 
                        data-input="<?=$contentInput?>[name]" 
                        data-value="<?=$content->name ?>"><?=$content->name ?></span>
                [<?=$content->type?>]
                <a class="up-fieldset">[&#8593;]</a>
                <a class="down-fieldset">[&#8595;]</a>
                <a class="remove-fieldset">[x]</a>
            </legend>
            <input  type="hidden" 
                    name="<?=$contentInput ?>[type]" value="<?=$content->type ?>" />
            <?php $content->edit( 
                null, 
                [ 'input' => $contentInput ]
            ); ?>
        </fieldset>
    <?php endforeach; endif; ?>
</div>

<?php $this->wc->witch()?->modules['cauldron']?->include(
    'cauldron/add.php', 
    [
        'input'     => $input."[content][new]",
        'cauldron'  => $this
    ]

);?>
