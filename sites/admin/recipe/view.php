<?php /** @var WC\Module $this */

$recipeName = $this->wc->request->param('recipe');
if( $recipeName ){
    $recipe = $this->wc->configuration->recipe( $recipeName );
}

if( !$recipe )
{
    $this->wc->user->addAlert([
        'level'     =>  'error',
        'message'   =>  "Recipe not found"
    ]);
    header( 'Location: '.$this->wc->website->getFullUrl('recipe') );
    exit();
}

$this->view();