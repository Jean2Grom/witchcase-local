<?php if( $element->craft()->attribute('image')->content() ): ?>
    <div class="image">
        <?=$element->craft()->attribute('image')->display()?>
        <p><?=$element->craft()->attribute('image')->content('title')?></p>
    </div>
<?php endif; ?>

<?php if( $element->craft()->attribute('embed-player')->content() ): ?>
    <div class="video">
        <?=$element->craft()->attribute('embed-player')->content()?>
    </div>
<?php endif; ?>

<?php if( $element->craft()->attribute('text')->content() ): ?>
    <div class="text">
        <?=$element->craft()->attribute('text')->content()?>
    </div>
<?php endif; ?>
