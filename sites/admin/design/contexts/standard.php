<!DOCTYPE html>
<?php /** @var WC\Context $this */

$this->addCssFile('base.css');
$this->addCssFile('header-footer.css');
$this->addCssFile('context-standard.css');
$this->addJsFile('fontawesome.js');
$this->addJsFile('context-standard.js');
$this->addJsLibFile('jquery-3.6.0.min.js');
?>
<html lang="fr-FR" dir="ltr">
    <head>
        <?php include $this->getIncludeDesignFile('head.php'); ?>
    </head>
    
    <body>
        <div class="container">
            <header><?php include $this->getIncludeDesignFile('header.php'); ?></header>
            
            <nav>
                <a class="side-nav-toggler">
                    <i class="fa fa-times"></i>
                </a>
                <?php if( $this->wc->witch('menu')->id ): foreach( $this->wc->witch('menu')->daughters() as $menuItemWitch ): ?>
                    <a href="<?=$menuItemWitch->getUrl() ?>">
                        <?=$menuItemWitch->name?>
                    </a>
                <?php endforeach; endif; ?>
            </nav>
            
            <main>
                <h1 class="breadcrumb__label" title="<?=$this->wc->witch()->data ?>">
                    <a href="javascript: location.reload();"><?=$this->wc->witch()->name ?></a>
                </h1>
                
                <div class="breadcrumb">
                    <?php if( $this->wc->witch()->id ): foreach( $breadcrumb as $i => $breadcrumbItem ): ?>
                        <?=( $i > 0 )? "&nbsp;>&nbsp": "" ?>
                        <span class="breadcrumb__item" title="<?=$breadcrumbItem['data'] ?>">
                            <a href="<?=$breadcrumbItem['href'] ?>">
                                <?=$breadcrumbItem['name'] ?>
                            </a>
                        </span>
                    <?php endforeach; endif; ?>
                </div>

                <div class="tabs">
                    <?php if( $this->wc->cairn->invokation("arborescence") ): ?>
                        <div class="tabs__item">
                           <a href="#tab-navigation">
                               <i class="fas fa-sitemap"></i> 
                               Navigation
                           </a>
                         </div>
                    <?php endif; ?>

                    <?php if( !$this->wc->witch()->id ): ?>
                        <div class="tabs__item selected">
                            <a href="#tab-current">
                                <i class="fas fa-bomb"></i> 404
                            </a>
                        </div>

                    <?php elseif( $this->tabs ): foreach( $this->tabs as $id => $tab ): ?>
                        <div    <?=($tab['hidden'] ?? null)? 'style="display: none;"': '' ?>
                                class="tabs__item <?=($tab['selected'] ?? null)? 'selected': '' ?>">
                            <a href="#<?=$id ?>">
                                <?=($tab['iconClass'] ?? null)? '<i  class="'.$tab['iconClass'].'"></i>': '' ?>
                                <?=$tab['text'] ?? '' ?>
                            </a>
                            <?php if( !empty($tab['close']) ): ?>
                                <a class="tabs__item__close" data-target="<?=$id ?>">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="tabs__item selected">
                            <a href="#tab-current">
                                <?php if( $this->wc->witch()->hasCraft() && $this->wc->witch()->invoke ): ?>
                                    <i  class="fas fa-hat-wizard"></i>
                                <?php elseif( $this->wc->witch()->hasCraft() ): ?>
                                    <i  class="fas fa-mortar-pestle"></i>
                                <?php elseif( $this->wc->witch()->invoke ): ?>
                                    <i  class="fas fa-hand-sparkles"></i>
                                <?php else: ?>
                                    <i class="fas fa-folder"></i>
                                <?php endif; ?>

                                <?=$this->wc->witch()->name ?>

                                <?php if( $this->wc->witch("target")->exist() ): ?>
                                    &nbsp;:
                                <?php elseif( $this->wc->witch("mother")->exist() ): ?>
                                    from&nbsp;:
                                <?php endif; ?>
                                <?=$this->wc->witch("target").$this->wc->witch("mother")?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tabs-target">
                    <?php if( !$this->wc->witch() ): ?>
                        <div class="tabs-target__item selected" id="tab-current">404</div>
                    <?php elseif( !$this->tabs ): ?>
                        <div class="tabs-target__item selected" id="tab-current"><?=$this->wc->cairn->invokation() ?></div>
                    <?php else: ?>
                        <?=$this->wc->cairn->invokation() ?>
                    <?php endif; ?>

                    <?php if( $this->wc->witch("arborescence") ): ?>
                        <div class="tabs-target__item" id="tab-navigation">
                            <?=$this->wc->cairn->invokation("arborescence") ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
            
            <?php if( $this->wc->witch("chooseWitch") ): ?>
                <?=$this->wc->cairn->invokation("chooseWitch") ?>
            <?php endif; ?>
            
            <footer><?php include $this->getIncludeDesignFile('footer.php'); ?></footer>
        </div>
        
        <?php foreach( $this->getJsFiles() as $jsFile ): ?>
            <script src="<?=$jsFile?>"></script>
        <?php endforeach; ?>        
    </body>
</html>