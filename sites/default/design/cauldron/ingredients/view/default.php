<?php /** @var WC\Ingredient $this */ 

if( is_object($this->value) || (is_array($this->value) && count($this->value) > 1) ){
    $this->wc->dump($this->value, "Ingredient to be displayed: ".$this->type);
}
else {
    echo $this->content();
}
