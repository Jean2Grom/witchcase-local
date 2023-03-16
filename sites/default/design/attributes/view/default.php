<?php 
if( count($this->values) > 1 ){
    $this->wc->dump($this->values);
}
else {
    echo $this->content();
}
