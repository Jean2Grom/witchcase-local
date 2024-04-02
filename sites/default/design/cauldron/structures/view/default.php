<?php /** @var WC\Cauldron $this */ ?>

<ul>
    <?php foreach( $this->content() ?? [] as $ingredient ): ?>
        <li>
            <?php if( $ingredient->name ): ?>
                <?=$ingredient->name." : " ?>
            <?php endif; ?>
            <?php $ingredient->display(); ?>
        </li>
    <?php endforeach; ?>
</ul>