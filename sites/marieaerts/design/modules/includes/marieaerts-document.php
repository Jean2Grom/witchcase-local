<a  class="document" 
    target="_blank"
    href="<?=$element->craft()->attribute('file')->content('file')?>">
    - <?=$element->craft()->attribute('file')->content('title')?>
    <?php if( $element->craft()->attribute('author')->content() ): ?>
        <br/>
        <em><?=$element->craft()->attribute('author')->content()?></em>
    <?php endif; ?>
    <?php if( $element->craft()->attribute('information')->content() ): ?>                    
        <?=$element->craft()->attribute('information')->content()?>
    <?php endif; ?>
</a>
