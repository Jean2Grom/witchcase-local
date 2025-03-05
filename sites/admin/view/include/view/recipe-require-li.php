<?php /** @var WC\Module $this */

$refuse = false;
if( $require['refuse'] ?? false )
{
    $refuse = [];
    foreach( $require['refuse'] as $refusedItem ){
        if( $this->wc->configuration->recipe( $refusedItem ) ){
            $refuse[] = '<a href="'.$this->witch->url([ 'recipe' => $refusedItem ]).'">'.$refusedItem.'</a>';
        }
        else {
            $refuse[] = $refusedItem;
        }
    }
}

$accept = false;
if( $require['accept'] ?? false )
{
    $accept = [];
    foreach( $require['accept'] as $acceptedItem ){
        if( $this->wc->configuration->recipe( $acceptedItem ) ){
            $accept[] = '<a href="'.$this->witch->url([ 'recipe' => $acceptedItem ]).'">'.$acceptedItem.'</a>';
        }
        else {
            $accept[] = $acceptedItem;
        }
    }
}

if( $accept ): ?>
    <li>
        <div>Accepted contents</div>
        <div><?=implode( ', ', $accept )?></div>
    </li>
<?php endif; 
if( $refuse ): ?>
    <li>
        <div>Refused contents</div>
        <div><?=implode( ', ', $refuse )?></div>
    </li>
<?php endif; 
if( $require['min'] ?? false ): ?>
    <li>
        <div>Minimum required</div>
        <div><?=$require['min'] ?? 0?></div>
    </li>
<?php endif; 
if( ($require['max'] ?? -1) >= 0 ): ?>
    <li>
        <div>Maximum allowed</div>
        <div><?=$require['max'] ?? 0?></div>
    </li>
<?php endif; ?>
