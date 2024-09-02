<?php 
/**
 * @var WC\Cauldron $this 
 * @var ?array $ingredients 
 * @var ?array $structures 
 * @var string $inputName
 */ 
?>

<div class="fieldsets-container">
    <?php foreach( $this->content() as $content ): 
        $integrationCountClass  = substr_count($this->editPrefix, '|') % 4;
        $structuredName         = $this->editPrefix.'|'.$content->getInputName(); 
        ?>
        <fieldset class="<?=$content->isIngredient()? 'ingredient': 'structure integration-'.$integrationCountClass?>">
            <legend>
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
            <?php $content->edit( 
                null, 
                [
                    'ingredients'   => $ingredients, 
                    'structures'    => $structures,
                    'inputName'     => $inputName.'|'.$content->getInputName(),
                ], 
                $this->getInputName() 
            ); ?>
        </fieldset>
    <?php endforeach; ?>

    <?php $this->wc->witch()?->modules['cauldron']?->include(
            'cauldron/add.php', 
            [ 
                'ingredients'   => $ingredients, 
                'structures'    => $structures, 
                'inputName'     => $inputName,
            ]
        );?>

</div>
