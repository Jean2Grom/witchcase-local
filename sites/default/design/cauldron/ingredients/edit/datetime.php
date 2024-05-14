<?php /** @var WC\Ingredient\DatetimeIngredient $this */ ?>

<input  type="datetime-local" 
        name="<?=$this->getInputName() ?>" 
        value="<?=$this->value?->format( 'Y-m-d\TH:i' ) ?>" />
