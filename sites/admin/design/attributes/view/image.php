<?php if( $srcFile ): ?>
    <p>
        <img src="<?=$srcFile?>" class="attribute-image-view" />
    </p>
    <p>
        <strong><?=$this->values['title']?></strong>
    </p>
<?php else: ?>
    Pas d'image
<?php endif; ?>