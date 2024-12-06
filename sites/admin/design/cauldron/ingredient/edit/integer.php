<?php 
/** 
 * @var WC\Cauldron\Ingredient\IntegerIngredient $this 
 * @var string $input 
 */
?>

<?php if( $this->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input?>[ID]" value="<?=$this->id ?>" />
<?php endif; ?>

<input  type="number" 
        name="<?=$input?>[value]" value="<?=$this?>" />
