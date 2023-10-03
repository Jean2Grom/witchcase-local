<?php 
$i = 0;
foreach( $this->witch->daughters() as $daughter ): ?>
    
    <?php if( !$daughter->craft() || $daughter->craft()->structure() !== "article-witchcase" ): 
        continue;
    endif; ?>
    
    <div id="bloc<?=$i%2+1?>">
        <h2><?=$daughter->craft()->attribute('headline')->content()?></h2>
        
        <div id="intro_bloc<?=$i%2+1?>">
            <?=$daughter->data?>
        </div>
        
        <div id="content_bloc<?=$i%2+1?>">
            
            <?php if( $daughter->craft()->attribute('image')->content() ): ?>
                <div class="schema">
                    <img src="<?=$daughter->craft()->attribute('image')->content("file")?>" 
                         alt="<?=$daughter->craft()->attribute('image')->content("title")?>" 
                         title="<?=$daughter->craft()->attribute('image')->content("title")?>" />
                </div>
            <?php endif; ?>
            
            <?php if( $daughter->craft()->attribute('body')->content() ): ?>
                <div class="schema">
                    <p>
                        <?=$daughter->craft()->attribute('body')->content()?>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if( $daughter->craft()->attribute('body-left')->content() ): ?>
                <div class="colone1">
                    <h4>
                        <?=$daughter->craft()->attribute('headline-left')->content()?>
                    </h4>
                    <p>
                        <?=$daughter->craft()->attribute('body-left')->content()?>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if( $daughter->craft()->attribute('body-center')->content() ): ?>
                <div class="colone2">
                    <h4>
                        <?=$daughter->craft()->attribute('headline-center')->content()?>
                    </h4>
                    <p>
                        <?=$daughter->craft()->attribute('body-center')->content()?>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if( $daughter->craft()->attribute('body-right')->content() ): ?>
                <div class="colone3">
                    <h4>
                        <?=$daughter->craft()->attribute('headline-right')->content()?>
                    </h4>
                    <p>
                        <?=$daughter->craft()->attribute('body-right')->content()?>
                    </p>
                </div> 
            <?php endif; ?>
        </div>
        
        <?php if( $daughter->craft()->attribute('link')->content() ): ?>
            <div class="bouton_doc">
                <a  <?=$daughter->craft()->attribute('link')->content('external')? 'target="_blank"': ''?>
                    href="<?=$daughter->craft()->attribute('link')->content('href')?>">
                    <p><?=$daughter->craft()->attribute('link')->content('text')?></p>
                </a>
            </div>
        <?php else: ?>
            <div class="no_doc"></div>
        <?php endif; ?>
    </div>
<?php 
$i++;
endforeach; ?>