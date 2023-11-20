<?php
$reset = $this->wc->cache->reset();

$this->wc->user->addAlerts([[
    'level'     =>  $reset? 'success': 'error',
    'message'   =>  $reset? 'Cache has been removed': 'Cache removing has failed',    
]]);

$this->setContext( 'empty' );
$this->view('back');
