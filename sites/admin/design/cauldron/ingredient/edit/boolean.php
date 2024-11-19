<?php 
/** 
 * @var WC\Ingredient\BooleanIngredient $this 
 * @var string $input 
 */
?>

<?php if( $this->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input?>[ID]" value="<?=$this->id ?>" />
<?php endif; ?>

<input  type='hidden' 
        name="<?=$input?>[value]" value='0' />
<input  type="checkbox" 
        name="<?=$input?>[value]" value="1" 
        <?=$this->value? 'checked': ''?> />

