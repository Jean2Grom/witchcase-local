<?php
$contextCraft = $this->wc->witch('home')->craft();

$metaTitle = $this->wc->witch('home')->name;
if( $contextCraft->attribute('meta-title')->content() ){
    $metaTitle = $contextCraft->attribute('meta-title')->content();
}
elseif( $contextCraft->attribute('title')->content() ){
    $metaTitle = $contextCraft->attribute('title')->content();
}

$menu = [];
foreach( $this->wc->witch('menu')->daughters() as $menuWitchDaughter )
{
    if( $menuWitchDaughter->hasInvoke() ){
        $menu[] = [ 
            'href'      =>  $menuWitchDaughter->getUrl(),
            'name'      =>  $menuWitchDaughter->name,
            'selected'  =>  ( $this->wc->witch() == $menuWitchDaughter ),
            'toggle'    =>  false,
            'submenu'   =>  [],
        ];
    }
    elseif( $menuWitchDaughter->daughters() )
    {
        $menuItem = [
            'href'      =>  false,
            'name'      =>  $menuWitchDaughter->name,
            'selected'  =>  in_array( $this->wc->witch(), $menuWitchDaughter->daughters() ),
            'toggle'    =>  $menuWitchDaughter->id,
            'submenu'   =>  [],
        ];
        
        foreach( $menuWitchDaughter->daughters() as $subMenuWitch ){
            if( $subMenuWitch->hasInvoke() ){
                $menuItem['submenu'][] = [ 
                    'href'      =>  $subMenuWitch->getUrl(),
                    'name'      =>  $subMenuWitch->name,
                    'selected'  =>  ( $this->wc->witch() == $subMenuWitch ),
                    'data'      =>  $subMenuWitch->data,
                ];
            }
        }
        
        if( !empty($menuItem['submenu']) ){
            $menu[] = $menuItem;
        }
    }
    
}


$this->addCssFile('styles.css');
$this->view();