<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.25, maximum-scale=5.0, target-densitydpi=device-dpi" />
    <meta name="description" content="<?=$contextCraft->attribute('meta-description')->content()?>" />
    <meta name="keywords" content="<?=$contextCraft->attribute('meta-keywords')->content()?>" />
    <meta charset="utf-8" />
    
    <title><?=$metaTitle?></title>    
    <?=$this->favicon()?>
    
    <?php foreach( $this->getCssFiles() as $cssFile ): ?>
        <link   rel="stylesheet" 
                type="text/css" 
                href="<?=$cssFile?>" />
    <?php endforeach; ?>
</head>
<body>
    <div id="container">
        <h1>
            <a href="<?=$this->wc->website->getUrl()?>">
                <?=$contextCraft->attribute('title')->content()?>
            </a>
        </h1>
        
        <nav class="menu">
            <ul>
                <?php foreach( $this->wc->witch('menu')->daughters() as $menuItem ): if( $menuItem->hasInvoke() || !empty($menuItem->daughters()) ):?>
                    <li>
                        <a  <?php if( $menuItem->hasInvoke() ): ?>
                                href="<?=$menuItem->getUrl() ?>">
                            <?php elseif( !empty($menuItem->daughters()) ): ?>
                                href=""
                                data-toggle="<?=$menuItem->id ?>">
                            <?php endif; ?>
                            
                            <?php if( $this->wc->witch() == $menuItem ): ?>
                                <em><?=$menuItem->name ?></em>
                            <?php elseif( in_array( $this->wc->witch(), $menuItem->daughters() ) ): ?>
                                <em><?=$menuItem->name ?></em>
                            <?php else: ?>
                                <?=$menuItem->name ?>
                            <?php endif; ?>
                        </a>
                        
                        <?php if( !empty($menuItem->daughters()) ): ?>
                            <ul class="submenu <?=!in_array( $this->wc->witch(), $menuItem->daughters() )? 'hidden': '' ?>"
                                id="sub-menu-<?=$menuItem->id ?>" >
                                <?php foreach( $menuItem->daughters() as $subMenuItem ): ?>
                                    <li>
                                        <a href="<?=$subMenuItem->getUrl() ?>">
                                            <strong>
                                                <?php if( $this->wc->witch() == $subMenuItem ): ?>
                                                    <em><?=$subMenuItem->name ?></em>
                                                <?php else: ?>
                                                    <?=$subMenuItem->name ?>
                                                <?php endif; ?>
                                            </strong>                            
                                        </a>
                                        <em><?=$subMenuItem->data ?></em>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endif; endforeach; ?>
            </ul>
        </nav>
        
        <div id="content">
            <?=$this->wc->cairn->invokation() ?>
            
            <div id="footer">
                <ul>
                    <li><?php $contextCraft->attribute('contact-email')->display(); ?></li>
                    <li>©marieaerts<?=date('Y')?></li>
                </ul>
            </div>
        </div>
        
    </div>
    
<script>
    document.querySelectorAll(".menu a").forEach(function( item ){
        item.addEventListener('click', function(e)
        {
            document.querySelectorAll(".submenu").forEach( function( subItem ){ 
               subItem.style.display = 'none';
            });
            
            if( item.dataset.toggle === undefined ){
                return true;
            }
            e.preventDefault();
                    
            document.getElementById( "sub-menu-" + item.dataset.toggle ).style.display = 'block';
            
            return false;
        });
    });
    
</script>
        
</body>
</html>