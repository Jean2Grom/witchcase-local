<?php /** @var WC\Attribute\TextAttribute $this */ 

$this->wc->website->context->addCssFile('../trumbowyg/dist/ui/trumbowyg.min.css');
$this->wc->website->context->addJsLibFile('jquery-3.6.0.min.js');
$this->wc->website->context->addJsLibFile('../trumbowyg/dist/trumbowyg.min.js');

$id = $this->type.'__'.$this->name;
?>

<textarea  id="<?=$id ?>" 
           name="<?=$this->tableColumns['value']?>"><?=$this->values['value']?></textarea>

<script>
    $('#<?=$id ?>').trumbowyg();
</script>