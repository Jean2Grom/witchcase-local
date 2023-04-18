<?php

//use WC\Localisation;

if( $this->values['fk_localisation'] > 0){   
    //$localisationTarget = new Localisation($this->module->wc, $this->values['fk_localisation']);
}
else {
    $localisationTarget = false;
}

$names = [];
foreach( $this->values['targets'] as $target ){
    if( !empty($target['name']) ){
        $names[] = $target['name'];
    }
    elseif( isset($target['attributes']["titre"]->values['string']) ){
        $names[] = $target['attributes']["titre"]->values['string'];
    }
}

include $this->module->getDesignFile('attributes/view/get_contents.php');