<div class="event">
    <?php if( $link ): ?>
        <h2>
            <a target="_blank" 
               href="<?=$link['href'] ?>">
                <?=$title?>
            </a>
        </h2>
    
    <?php else: ?>
        <h2><?=$title?></h2>
        
    <?php endif; ?>
    
    <em><?=$head?></em>
    
    <?php if( $link && $link['text'] ): ?>
        <a  class="right"
            target="_blank" 
            href="<?=$link['href'] ?>">
            <?=$link['text'] ?>
        </a>
    <?php endif; ?>
    
    <p><?=$body?></p>
    
    <?php if(  $link && $image ): ?>
        <a target="_blank" 
           href="<?=$link['href'] ?>">
            <?=$image->display()?>
        </a>
    <?php elseif( $image ): ?>
        <?=$image->display()?>
    <?php endif; ?>
</div>