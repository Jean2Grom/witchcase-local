<?php /** @var WC\Ingredient $this */ ?>

<input  type='hidden' value='0' name="<?=$this->getInputName() ?>" >
<input  type="checkbox" value="1" 
        name="<?=$this->getInputName() ?>" 
        <?=$this->value? 'checked': ''?> />

