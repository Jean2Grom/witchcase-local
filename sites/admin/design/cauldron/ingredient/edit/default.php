<?php 
/** 
 * @var WC\Cauldron\Ingredient $this 
 * @var string $input 
 */
?>

<?php if( $this->exist() ): ?>
        <input  type="hidden" 
                name="<?=$input?>[ID]" value="<?=$this->id ?>" />
<?php endif; ?>

<input  type="text" 
        name="<?=$input?>[value]" 
        value="<?=$this?>" />