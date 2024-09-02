<?php /** @var WC\Cauldron $this */ ?>

<ul>
    <?php foreach( $this->content() ?? [] as $content ): ?>
        <li>
            <h4>
                <?php if( $content->name ): ?>
                    <?=$content->name ?>
                <?php endif; ?>
                <?="[".$content->type."] " ?>
            </h4>
            <?php $content->edit( null, $this->getInputName() ); ?>
        </li>
    <?php endforeach; ?>
</ul>