<?php /** @var WC\Ingredient $this */ 

if( count($this->valueFields) == 1 ): ?>
    <input  type="text" 
            name="<?=$prefix?>i#<?=$this->type ?>__<?=$this->name.$suffix?>" 
            id="<?=$prefix?>i#<?=$this->type ?>__<?=$this->name.$suffix ?>" 
            value="<?=$this ?>" />
<?php else: foreach( array_keys($this->valueFields) as $field ): ?>
    <h5><?=$field ?></h5>
    <input  type="text" 
            name="<?=$prefix?>i#<?=$this->type ?>__<?=$field ?>#<?=$this->name.$suffix ?>" 
            id="<?=$prefix?>i#<?=$this->type ?>__<?=$field ?>#<?=$this->name.$suffix ?>" 
            value="<?=$this->content( $field ) ?>" />
<?php endforeach; endif; 