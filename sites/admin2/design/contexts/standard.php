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
                <?php foreach( $this->wc->witch('menu')->daughters() as $menuItemWitch ): ?>
                    <a href="<?=$menuItemWitch->geturl() ?>">
                        <?=$menuItemWitch->name?>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>
        
        <!-- content -->
        <section>
            <div class="breadcrumb">
                <h1 class="breadcrumb__label" title="<?=$this->wc->witch()->data ?>">
                    <a href="javascript: location.reload();"><?=$this->wc->witch()->name ?></a>
                </h1>
                
                <?php foreach( $breadcrumb as $i => $breadcrumbItem ): ?>
                    <?=( $i > 0 )? "&nbsp;>&nbsp": "" ?>
                    <span class="breadcrumb__item" title="<?=$breadcrumbItem['data'] ?>">
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
                
                <?php if( !$this->wc->witch() ): ?>
                    <a class="tabs__item selected" href="#tab-current">                   
                        404
                    </a>
                <?php elseif( $this->tabs ): foreach( $this->tabs as $id => $tab ): ?>
                    <a class="tabs__item <?=($tab['selected'] ?? null)? 'selected': '' ?>" 
                       href="#<?=$id ?>">
                        <?=($tab['iconClass'] ?? null)? '<i  class="'.$tab['iconClass'].'"></i>': '' ?>
                        <?=$tab['text'] ?? '' ?>
                    </a>
                <?php endforeach; else: ?>
                    <a class="tabs__item selected" href="#tab-current">
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
                <?php endif; ?>
            </div>
            
            <div class="tabs-target">
                <?php if( !$this->wc->witch() ): ?>
                    <div class="tabs-target__item selected" id="tab-current">404</div>
                <?php elseif( !$this->tabs ): ?>
                    <div class="tabs-target__item selected" id="tab-current"><?=$this->wc->witch()->result() ?></div>
                <?php else: ?>
                    <?=$this->wc->witch()->result() ?>
                <?php endif; ?>
                
                <?php if( $this->wc->witch("arborescence") ): ?>
                    <div class="tabs-target__item" id="tab-navigation">
                        <?=$this->wc->witch("arborescence")->result("arborescence") ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- footer -->
        <?php include $this->getIncludeDesignFile('footer.php'); ?>
        
<style>

    #choose-witch {
        display: none;
        position: fixed;
        top: 30%;
        left: 30%;
        width: 40%;
        background-color: #FFF;
        padding: 0 15px 15px 15px;
        border: 1px solid #000;
        border-radius: 5px;
    }
        #choose-witch .close {
            cursor: pointer;
            float: right;
            margin-right: 5px;
        }

</style>
<div id="choose-witch">
    <h3>
        Choisir un emplacement
        <a class="close">
            <i class="fa fa-times"></i>
        </a>
    </h3>
    
    <div class="arborescence-menu-container"></div>
    
    <input type="hidden" id="choose-witch-target" value="" />
</div>

<script>
$(document).ready(function()
{
    
    // Open popin for witch selection for policy
    $('#policies-container').on('click', '.new-profile-witch', function(){
        $(this).parents('.tabs-target__item').children().not('#choose-witch').css('filter', "blur(4px)");
        let targetId = $(this).parents('.new-profile-police').attr('id');
        $('#choose-witch-target').val( targetId );
        $('#choose-witch').show();
        
        return false;
    });
    
    // Unset policy witch
    $('#policies-container').on('click', '.unset-new-profile-witch', function(){
        $(this).parents('.new-profile-police').find('.new-profile-witch-id').val( '0' );
        let label = $(this).parents('.new-profile-police').find('.new-profile-witch').data('unset');
        $(this).parents('.new-profile-police').find('.new-profile-witch').html( label );
        
        $(this).hide();
    });
    
    // Close select policy witch popin
    $('#choose-witch .close').click(function(){
        $(this).parents('.tabs-target__item').children().not('#choose-witch').css('filter', "blur(0)");
        $('#choose-witch-target').val('');
        $('#choose-witch').hide();
    });
    
    // Select witch for policy in popin
    $('#choose-witch').on('click', 'a.arborescence-level__witch__name', function(){
        let witchId     = $(this).parents('.arborescence-level__witch').data('id');
        let witchLabel  = $(this).html();
        let targetId    = $('#choose-witch-target').val();
        
        $('#'+targetId).find('.new-profile-witch-id').val( witchId );
        $('#'+targetId).find('.new-profile-witch').html( witchLabel );
        $('#'+targetId).find('.unset-new-profile-witch').show();
        
        $('#choose-witch .close').trigger('click');
        
        return false;
    });
    
    // Policy witch popin navigation
    //breadcrumb = [ breadcrumb[0] ];
    
    $('#choose-witch').on('click',  '.arborescence-level__witch__daughters-display', function(){
        let levelTarget = $(this).parents('.arborescence-menu-container');
        
        $(levelTarget).animate({scrollLeft: $(levelTarget).find('.arborescence-level').last().position().left}, 500);
    });
    
});
</script>

        
    </body>
</html>