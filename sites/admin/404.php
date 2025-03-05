<?php /** @var WC\Module $this */

if( $this->wc->user->connexion ){
    $this->setContext('standard');
}

$this->view();