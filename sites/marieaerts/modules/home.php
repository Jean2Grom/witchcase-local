<?php

/*$contenu = false;
if( isset($target->attributes['contenu']) )
{   $contenu = $target->attributes['contenu']->content();   }

if( $contenu
    || strcmp($target->structure, 'work') == 0
){
    include $module->getControllerFile('modules/view/work.php');
}
elseif( strcmp($target->structure, 'page-folder') == 0 )
{
    include $module->getControllerFile('modules/view/page-folder.php');
}
else
{
    $news = $target->attributes['remontee-de-news']->values['targets'];
    foreach($news as $i => $news_item)
    {
        $image = $news_item['attributes']['image']->getFile();

        if( $image )
        {   $news[$i]['attributes']['image']->values['image'] = $image; }
    }
    
    include $module->getDesignFile();
}
 * 
 */
$this->view();