<?php

if( $this->wc->user->connexion ){
    $this->setContext('standard');
}

$this->view();