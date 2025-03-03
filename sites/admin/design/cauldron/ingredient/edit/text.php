<?php 
/** 
 * @var WC\Cauldron\Ingredient\TextIngredient $this 
 * @var string $input 
 */

$this->wc->website->context->addCssFile('../trumbowyg/dist/ui/trumbowyg.min.css');
$this->wc->website->context->addJsLibFile('jquery-3.6.0.min.js');
$this->wc->website->context->addJsLibFile('../trumbowyg/dist/trumbowyg.min.js');

$id = uniqid('text__'); 
?>

<textarea  id="<?=$id ?>" 
           name="<?=$input?>[value]"><?=$this->value ?? ''?></textarea>
<script>
    $(document).ready( function(){
        $('#<?=$id ?>').trumbowyg();
    });
</script>