<?php
$eligibleForHome    = [];
$maxPriority        = 0;
foreach( $this->witch->daughters  as $daughter )
{
    if( !$daughter->hasCraft() ){
        continue;
    }
    
    if( $daughter->priority > $maxPriority )
    {
        $eligibleForHome    = [];
        $maxPriority        = $daughter->priority;
    }
    
    if( $daughter->priority == $maxPriority ){
        $eligibleForHome[] = $daughter;
    }
}

$highlight = $eligibleForHome[ rand(0, count($eligibleForHome) - 1) ];

$redirectionURL = null;
foreach( $highlight->craft()->getWitches() as $highlightWitch ){
    if( $this->wc->witch('menu')->isParent( $highlightWitch ) )
    {
        $redirectionURL = $highlightWitch->mother()->getUrl();
        break;
    }
}

if( $redirectionURL )
{
    $image  = $highlight->craft()->attribute('image');
    $video  = $highlight->craft()->attribute('embed-player');
    $text   = $highlight->craft()->attribute('text');    
}
else
{
    if( $highlight->craft()->attribute('title') ){
        $title = $highlight->craft()->attribute('title')->content();
    }
    if( empty($title) && $highlight->craft()->attribute('name') ){
        $title = $highlight->craft()->attribute('name')->content();
    }
    if( empty($title) ){
        $title = $highlight->name;
    }
    
    $head = "";
    if( $highlight->craft()->attribute('head') ){
        $head = $highlight->craft()->attribute('head')->content();
    }

    $image = $highlight->craft()->attribute('image');

    $body = "";
    if( $highlight->craft()->attribute('body') ){
        $body = $highlight->craft()->attribute('body')->content();
    }
    
    $link = false;
    if( $highlight->craft()->attribute('link') ){
        $link = $highlight->craft()->attribute('link')->content();
    }    
}

$this->view();