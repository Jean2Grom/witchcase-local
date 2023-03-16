<?php if( $srcFile ): ?>
    <p>
        <img src="<?=$srcFile?>" class="imageAttributeView" />
    </p>
    <p>
        <strong><?=$this->values['title']?></strong>
    </p>
<?php else: ?>
    Pas d'image
<?php endif; ?>