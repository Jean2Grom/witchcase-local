<?php
$faviconFileHtmlPath = $this->getImageFile("favicon.ico");
if( $faviconFileHtmlPath )
{
    $faviconFile    = substr( $this->getImageFile("favicon.ico"), 1);
    $faviconMime    = mime_content_type($faviconFile) ?? '';
    $faviconContent = base64_encode( file_get_contents($faviconFile) ) ?? '';
}

$baseUri        = $this->website->baseUri;
$currentWitch   = $this->wc->witch();

$breadcrumb = [
    [
        "name"  => $this->wc->witch()->name,
        "data"  => $this->wc->witch()->data,
        "href"  => "javascript: location.reload();",
    ]
];

$breadcrumbWitch    = $currentWitch->mother();
while( !empty($breadcrumbWitch) )
{
    $url = $breadcrumbWitch->getUrl();
    
    if( $url ){
        $breadcrumb[]   = [
            "name"  => $breadcrumbWitch->name,
            "data"  => $breadcrumbWitch->data,
            "href"  => $url,
        ];        
    }
    
    $breadcrumbWitch    = $breadcrumbWitch->mother();
}
$breadcrumb = array_reverse($breadcrumb);


$this->view();