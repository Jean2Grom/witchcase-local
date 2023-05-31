<?php 
    $this->addCssFile('header.css');
    $this->addCssFile('context-standard.css');
    $this->addJsFile('fontawesome.js');
    $this->addJsFile('context-standard.js');
    $this->addJsLibFile('jquery-3.6.0.min.js');
?>
<!DOCTYPE html>
<html lang="fr-FR" dir="ltr">
    <head>
        <?php include $this->getIncludeDesignFile('head.php'); ?>
    </head>
    
    <body>
        <?php include $this->getIncludeDesignFile('header.php'); ?>
        
        <?php if( $this->wc->witch('menu') ): ?>
            <nav>
                <?php foreach( $this->wc->witch('menu')->daughters as $menuItemWitch ): ?>
                    <a href="<?=$menuItemWitch->geturl() ?>">
                        <?=$menuItemWitch->name?>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>
        
        <!-- content -->
        <section>
            <div class="breadcrumb">
                <span class="breadcrumb__label">
                    Breadcrumb&nbsp;:
                </span>
                
                <?php foreach( $breadcrumb as $i => $breadcrumbItem ): ?>
                    <?=( $i > 0 )? "&nbsp;>&nbsp": "" ?>
                    <span class="breadcrumb__item">
                        <a href="<?=$breadcrumbItem['href'] ?>">
                            <?=$breadcrumbItem['name'] ?>
                        </a>
                    </span>
                <?php endforeach; ?>
                <div class="clear"></div>
            </div>
            
            <div class="tabs">
                <?php if( $this->wc->witch("arborescence") ): ?>
                    <a class="tabs__item" href="#tab-navigation">
                        <i class="fas fa-sitemap"></i>
                        Navigation
                    </a>
                <?php endif; ?>
                <a class="tabs__item selected" href="#tab-current">
                    <?php if( !$this->wc->witch() ): ?>
                        404
                    <?php else: ?>
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
                        <?=$this->wc->witch("target")->id?  "&nbsp;:": ($this->wc->witch("mother")->id? "depuis l'élément&nbsp;:": "") ?>
                        <?=$this->wc->witch("target")->name ?? $this->wc->witch("mother")->name ?? "" ?>                        
                    <?php endif; ?>
                </a>
                <div class="clear"></div>
            </div>
            
            <div class="tabs-target">
                <div class="tabs-target__item selected" id="tab-current">
                    <?php if( !$this->wc->witch() ): ?>
                        404
                    <?php else: ?>
                        <?=$this->wc->witch()->result() ?>
                    <?php endif; ?>
                </div>
                <?php if( $this->wc->witch("arborescence") ): ?>
                    <div class="tabs-target__item" id="tab-navigation">
                        <?=$this->wc->witch("arborescence")->result("arborescence") ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- footer -->
        <?php include $this->getIncludeDesignFile('footer.php'); ?>                
    </body>
</html>