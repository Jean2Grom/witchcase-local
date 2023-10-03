<?php
if( $this->witch->craft() )
{
    $craft = $this->witch->craft();
    if( $craft->attribute('background') && $craft->attribute('background')->content() ){
        $this->addContextVar( 'backgroundImage', $craft->attribute('background')->content('file') );
    }
    
    if( $craft->attribute('headline') && $craft->attribute('headline')->content() ){
        $this->addContextVar( 'headlineText', $craft->attribute('headline')->content() );
    }
    
    if( $craft->attribute('body') && $craft->attribute('body')->content() ){
        $this->addContextVar( 'bodyText', $craft->attribute('body')->content() );
    }
}

include $this->getDesignFile();


