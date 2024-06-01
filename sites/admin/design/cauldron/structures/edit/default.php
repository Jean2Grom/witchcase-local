<?php /** @var WC\Cauldron $this */ ?>

<ul>
    <?php foreach( $this->content() ?? [] as $content ): ?>
        <li>
            <h4>
                <?php if( $content->name ): ?>
                    <?=$content->name ?>
                <?php endif; ?>
                <?="[".$content->type."] " ?>
                <a class="up-fieldset-element">[&#8593;]</a>
                <a class="down-fieldset-element">[&#8595;]</a>
                <a class="remove-fieldset-element">[x]</a>
            </h4>
            <?php $content->edit( null, $this->getInputName(false) ); ?>
        </li>
    <?php endforeach; ?>
</ul>