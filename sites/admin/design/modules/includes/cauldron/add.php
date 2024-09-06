<?php 
/**
 * @var ?array $ingredients 
 * @var ?array $structures 
 * @var string $input
 */
?>
<div class="cauldron-add-actions">
    <div class="add-form" data-input="<?=$input ?>">
        <h4>
            Add 
            <a class="hide-form">[x]</a>
        </h4>
        <select>
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
        <input type="text" value="" />
        <button class="disabled" 
                data-action="save" 
                data-target="edit-action">
            <i class="fa fa-save"></i>
            Save
        </button>
    </div>
    
    <button class="show-form">
        <i class="fa fa-plus" aria-hidden="true"></i>
    </button>
</div>
