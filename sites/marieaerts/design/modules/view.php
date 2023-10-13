<h2><?=$title ?></h2>

<?php if( !empty($description) ): ?>
    <p class="description"><?=$description ?></p>
<?php endif; ?>

<?php if( $image ): ?>
    <?=$image->display()?>
<?php endif; ?>

<?php if( !empty($body) ): ?>
    <p><?=$body ?></p>
<?php endif; ?>

<div class="elements">
    <?php foreach( $this->witch->daughters() as $daughter ): ?>
        <?php if( $daughter->craft() ): 
            $element = $daughter;
            include $this->getIncludeDesignFile( $daughter->craft()->structure().'.php' ); 
        else: ?>
            <h3><?=$daughter->name ?></h3>
            <?php foreach( $daughter->daughters() as $subelement ): 
                $element = $subelement;
                include $this->getIncludeDesignFile( $subelement->craft()->structure().'.php' ); ?> 
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

