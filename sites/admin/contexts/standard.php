<?php /** @var WC\Context $this */

$initialWitch = $this->wc->witch("target") ?? $this->wc->witch();

if( $this->breadcrumb ){
    $breadcrumb = $this->breadcrumb;
}
else
{
    $breadcrumb         = [];
    $breadcrumbWitch    = $initialWitch;
    while( !empty($breadcrumbWitch) )
    {
        if( $breadcrumbWitch  === $initialWitch ){
            $url    = "javascript: location.reload();";
        }
        else {
            $url    = $breadcrumbWitch->url();
        }
        
        if( $url ){
            $breadcrumb[]   = [
                "name"  => $breadcrumbWitch->name,
                "data"  => $breadcrumbWitch->data,
                "href"  => $url,
            ];
        }

        if( $this->wc->witch('root') === $breadcrumbWitch ){
            break;
        }

        $breadcrumbWitch    = $breadcrumbWitch->mother();    
    }
    
    $breadcrumb = array_reverse($breadcrumb);
}

$this->view();