<?php
$currentWitch   = $this->wc->witch();

if( $this->breadcrumb ){
    $breadcrumb = $this->breadcrumb;
}
else
{
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
}

$this->view();