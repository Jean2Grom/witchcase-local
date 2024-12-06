<?php 
/** 
 * @var WC\Cauldron\Ingredient\DatetimeIngredient $this 
 * @var string $input 
 */
?>

<?php if( $this->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input?>[ID]" value="<?=$this->id ?>" />
<?php endif; ?>

<input  type="datetime-local" 
        name="<?=$input?>[value]" value="<?=$this->value?->format( 'Y-m-d\TH:i' ) ?>" />
