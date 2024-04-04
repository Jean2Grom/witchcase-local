<?php /** @var WC\Cauldron $this */ ?>

<ul>
    <?php foreach( $this->content() ?? [] as $ingredient ): ?>
        <li style="display: flex;justify-content: space-between;">
            <div>
                <?php if( $ingredient->name ): ?>
                    <?=$ingredient->name ?>
                <?php endif; ?>
                <?="[".$ingredient->type."] " ?>
            </div>
            <div><?php $ingredient->display( false, 40 ); ?></div>
        </li>
    <?php endforeach; ?>
</ul>