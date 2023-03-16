<?php
if( filter_has_var(INPUT_POST, "login") 
        && $this->wc->user->connexion   ){
    header('location : '.$this->wc->request->protocole.'://'.$this->wc->website->currentAccess);
    exit();
}

$alerts = $this->wc->user->getAlerts();
foreach( $this->wc->user->loginMessages as $message ){
    $alerts[] = [
        'level'     =>  'warning',
        'message'   =>  $message,
    ];
}

$this->wc->user->disconnect();

$this->setContext('login');

include $this->getDesignFile();