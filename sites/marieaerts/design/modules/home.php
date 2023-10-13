<?php if( !$redirectionURL ): ?>
    <?php include $this->getIncludeDesignFile( $highlight->craft()->structure().'.php' ); ?>

<?php else: ?>
        <a href="<?=$redirectionURL?>">
            <?php if( $image ): ?>
                <?=$image->display()?>
            <?php endif; ?>

            <?php if( $video && $video->content() ): ?>
                <div class="video"><?=$video->content()?></div>
            <?php endif; ?>

            <?php if( $text && $text->content() ): ?>
                <div class="text">
                    <?=$text->content()?>
                </div>
            <?php endif; ?>
        </a>
<?php endif; ?>
