<?php 
/** 
 * @var WC\Ingredient $this 
 * @var ?string $input 
 */

use WC\Tools;

$input = $input ?? $this->type.'['.Tools::cleanupString( $this->name ).']'; 
?>

<fieldset>
        <?php if( $this->exist() ): ?>
                <input  type="hidden" 
                        name="<?=$input?>[ID]" value="<?=$this->id ?>" />
        <?php endif; ?>

        <label for="<?=$input?>#name">Name</label>
        <input  type="text" 
                id="<?=$input?>#name" 
                name="<?=$input?>[name]" 
                value="<?=$this->name ?>" />

        <label for="<?=$input?>#value">Value</label>
        <input  type="text" 
                id="<?=$input?>#value" 
                name="<?=$input?>[value]" 
                value="<?=$this?>" />

        <label for="<?=$input?>#priority">Priority</label>
        <input  type="number" 
                id="<?=$input?>#priority" 
                name="<?=$input?>[priority]" 
                value="<?=$this?>" />
</fieldset>