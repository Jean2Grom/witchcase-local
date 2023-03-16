<!DOCTYPE html>
<html lang="fr-FR" dir="ltr">
    <head>
        <?php include $this->getIncludeDesignFile('head.php'); ?>
    </head>
    
    <body>
        <?php include $this->getIncludeDesignFile('header.php'); ?>
        
        <?php if( isset($menu) ): ?>
            <nav>
                <?php foreach( $menu as $item ): ?>
                    <a href="<?=$item['href']?>">
                        <?=$item['name']?>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>
        
        <!-- content -->
        <section>
            <div class="breadcrumb">
                <span class="breadcrumb__label">
                    Vous &ecirc;tes ici&nbsp;:
                </span>
                
                <?php foreach( $breadcrumb as $i => $breadcrumbItem ): ?>
                    <?php if( $i > 0 ): ?>
                        &nbsp;>&nbsp;
                    <?php endif; ?>
                    <span class="breadcrumb__item">
                        <?php if( $breadcrumbItem['href'] ): ?>
                            <a href="<?=$breadcrumbItem['href'] ?>">
                                <?=$breadcrumbItem['name'] ?>
                            </a>
                        <?php else: ?>
                            <?=$breadcrumbItem['name'] ?>
                        <?php endif; ?>
                    </span>
                <?php endforeach; ?>
                <div class="clear"></div>
            </div>
            
            <div class="tabs">
                <?php if( !empty($this->website->witches["arborescence"]) ): ?>
                    <a class="tabs__item" href="#tab-navigation">
                        <i class="fas fa-sitemap"></i>
                        Navigation
                    </a>
                <?php endif; ?>
                <a class="tabs__item selected" href="#tab-current">
                    <?php if( empty($this->website->witches["current"]) ): ?>
                        404
                    <?php else: ?>
                        <?php if( $this->website->witches["current"]->hasTarget() && !empty($this->website->witches["current"]->invoke) ): ?>
                            <i  class="fas fa-hat-wizard"></i>
                        <?php elseif( $this->website->witches["current"]->hasTarget() ): ?>
                            <i  class="fas fa-mortar-pestle"></i>
                        <?php elseif( !empty($this->website->witches["current"]->invoke) ): ?>
                            <i  class="fas fa-hand-sparkles"></i>
                        <?php else: ?>
                            <i class="fas fa-folder"></i>
                        <?php endif; ?>
                        
                        <?=$this->website->witches["current"]->name ?>
                        
                        <?php if(  !empty($this->website->witches["target"]) ): ?>
                            :&nbsp;<?=$this->website->witches["target"]->name ?>
                        <?php elseif(  !empty($this->website->witches["mother"]) ): ?>
                            depuis l'élément&nbsp;:
                            <?=$this->website->witches["mother"]->name ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </a>
                <div class="clear"></div>
            </div>
            
            <div class="tabs-target">
                <div class="tabs-target__item selected" id="tab-current">
                    <?php if( empty($this->website->witches["current"]) ): ?>
                        404
                    <?php else: ?>
                        <?=$this->website->witches["current"]->result ?>
                    <?php endif; ?>
                </div>
                <?php if( !empty($this->website->witches["arborescence"]) ): ?>
                    <div class="tabs-target__item" id="tab-navigation">
                        <?=$this->website->witches["arborescence"]->modules["arborescence"]->result ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        
        <!-- footer -->
        <?php include $this->getIncludeDesignFile('footer.php'); ?>
        
        <script type="text/javascript">
            var tabs = document.getElementsByClassName("tabs__item");

            var selectTab = function( event ){
                event.preventDefault();

                if( this.className.includes("selected") ){
                    return false;
                }

                let seletedTabs = document.getElementsByClassName("tabs__item selected");
                for( let i = 0; i < seletedTabs.length; i++ ){
                    seletedTabs[i].classList.remove("selected");
                }

                let seletedTargets = document.getElementsByClassName("tabs-target__item selected");
                for( let i = 0; i < seletedTargets.length; i++ ){
                    seletedTargets[i].classList.remove("selected");
                }

                this.classList.add("selected");

                let targetId    = this.getAttribute("href").substring(1);
                let target      = document.getElementById( targetId );

                target.classList.add("selected");

                return false;
            };

            for( let i = 0; i < tabs.length; i++ ){
                tabs[i].addEventListener( 'click', selectTab, {passive: false} );
            }

            if( window.top.location.hash != undefined && window.top.location.hash != '' )
            {
                let tabFired = document.querySelectorAll( "[href='"+window.top.location.hash+"']" );

                let evObj = document.createEvent('Events');
                evObj.initEvent('click', true, false);
                tabFired[0].dispatchEvent(evObj);
                console.log(tabFired[0]);
            }
        </script>
        
    </body>
</html>