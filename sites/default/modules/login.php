<?php /** @var WC\Module $this */ 

if( $this->wc->request->param("action") == "login" && $this->wc->user->connexion )
{
    header( 'Location: '.$this->wc->website->getFullUrl() );
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

$this->view();