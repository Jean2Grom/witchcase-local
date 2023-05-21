<?php if( !empty($alerts) ): foreach( $alerts as $alert ): ?>
    <p class="alert-message <?=$alert['level']?>">
        <?=$alert['message']?>
    </p>
<?php endforeach; endif; ?>