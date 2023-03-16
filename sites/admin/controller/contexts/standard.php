<?php
$this->addJsFile('fontawesome.js');
$this->addJsLibFile('jquery-3.6.0.min.js');

$faviconFile    = substr( $this->getImageFile("favicon.ico"), 1);
$faviconMime    = mime_content_type($faviconFile) ?? '';
$faviconContent = base64_encode( file_get_contents($faviconFile) ) ?? '';

$baseUri        =   $this->website->baseUri;

$currentWitch   = $this->website->witches["current"];

$selfHref = $baseUri.$currentWitch->url;
if( !empty($this->wc->request->queryString) ){
    $selfHref .= '?'.$this->wc->request->queryString;
}
$breadcrumb = [
    [
        "name"  => $currentWitch->name,
        "href"  => $selfHref,
    ]
];
$breadcrumbWitch    = $currentWitch->mother;
while( !empty($breadcrumbWitch) )
{
    $uri = $baseUri;
    if( $breadcrumbWitch->uri != $uri || $this->website->name != $breadcrumbWitch->site ){
        $uri .= "/view?id=".$breadcrumbWitch->id;
    }
    
    $breadcrumb[]   = [
        "name"  => $breadcrumbWitch->name,
        "href"  => $uri,
    ];
    $breadcrumbWitch    = $breadcrumbWitch->mother;
}
$breadcrumb = array_reverse($breadcrumb);


$menu = [
    [
        'name'  =>  "Explorer", 
        'href'  =>  $baseUri, 
        'class' =>  ''
    ],
    [
        'name'  =>  "Profiles",
        'href'  =>  $baseUri."/profiles", 
        'class' =>  ''
    ],
    [
        'name'  =>  "Structures",
        'href'  =>  $baseUri."/structures", 
        'class' =>  'last'
    ],
];

include $this->getDesignFile();
