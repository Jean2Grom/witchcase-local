<?php
namespace WC\Cauldron;

use WC\Cauldron;

class ConnexionCauldron extends Cauldron
{
    
    function readInputs( mixed $inputs=null ): self
    {
        $this->wc->debug($inputs, 'test');

        return $this;
    }

}

