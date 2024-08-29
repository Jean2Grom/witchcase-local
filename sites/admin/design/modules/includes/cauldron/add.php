<?php 
/**
 * @var ?array $ingredients 
 * @var ?array $structures 
 * @var string $inputName
 */
?>
AAAAAA
<?=$inputName ?>
BBBBBB
<div class="cauldron-add-actions">
    <fieldset class="add-form">
        <legend>
            Add 
            <a class="hide-form">[x]</a>
        </legend>
        <select name="<?=$inputName ?>__add-type">
            <option value="">Select type</option>
            <?php if( $ingredients ): ?>
                <optgroup label="ingredients">
                    <?php foreach( $ingredients ?? [] as $ingredient ): ?>
                        <option value="<?=$ingredient?>">
                            <?=$ingredient?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endif; ?>
            <?php if( $structures ): ?>
                <optgroup label="structures">
                    <?php foreach( $structures ?? [] as $structure ): ?>
                        <option value="<?=$structure->name?>">
                            <?=$structure->name?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endif; ?>
        </select>
        <input type="text" name="<?=$inputName ?>__add-name" value="" />
        <button class="disabled" 
                data-action="save" 
                data-target="edit-action">
            <i class="fa fa-save"></i>
            Save
        </button>
    </fieldset>
    
    <button class="show-form">
        <i class="fa fa-plus" aria-hidden="true"></i>
    </button>
</div>
