<h2><?=$this->witch->craft()->attribute('name')->content()?></h2>

<?php if( $this->witch->craft()->attribute('head')->content() ): ?>
    <h3><?=$this->witch->craft()->attribute('head')->content()?></h3>
<?php endif; ?>

<?php if( $this->witch->craft()->attribute('description')->content() ): ?>
    <p><?=$this->witch->craft()->attribute('description')->content()?></p>
<?php endif; ?>

<div class="elements">
    <?php foreach( $this->witch->daughters ?? [] as $element ): ?>
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

    <?php endforeach; ?>
</div>

