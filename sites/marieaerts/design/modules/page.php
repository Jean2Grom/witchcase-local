<h2><?=$this->witch->craft()->attribute('title')->content()?></h2>

<div class="elements">
    <?php if( $this->witch->craft()->attribute('image')->content() ): ?>
        <div class="image">
            <?=$this->witch->craft()->attribute('image')->display()?>
            <p><?=$this->witch->craft()->attribute('image')->content('title')?></p>
        </div>
    <?php endif; ?>

    <?php if( $this->witch->craft()->attribute('body')->content() ): ?>
        <div class="text">
            <?=$this->witch->craft()->attribute('body')->content()?>
        </div>
    <?php endif; ?>
</div>

<div id="documents">
    <?php foreach( $this->witch->daughters() as $element ): ?>
        <h4><?=$element->name ?></h4>
        <p>
            <?php foreach( $element->daughters() as $document ): ?>
            
                - 
                <a href="<?=$document->craft()->attribute('file')->content('file')?>">
                    <?=$document->craft()->attribute('file')->content('title')?>
                </a>
                <?php if( $document->craft()->attribute('author')->content() ): ?>
                    <br/>
                    <em><?=$document->craft()->attribute('author')->content()?></em>
                <?php endif; ?>
                <?php if( $document->craft()->attribute('information')->content() ): ?>                    
                    <?=$document->craft()->attribute('information')->content()?>
                <?php endif; ?>
                <br/>
                
            <?php endforeach; ?>
        </p>
    <?php endforeach; ?>
</div>

