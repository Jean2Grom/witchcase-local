<?php /** @var WC\Module $this */

$refuse = false;
if( $require['refuse'] ?? false )
{
    $refuse = [];
    foreach( $require['refuse'] as $refusedItem ){
        if( $this->wc->configuration->structure( $refusedItem ) ){
            $refuse[] = '<a href="'.$this->witch->url([ 'structure' => $refusedItem ]).'">'.$refusedItem.'</a>';
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
        if( $this->wc->configuration->structure( $acceptedItem ) ){
            $accept[] = '<a href="'.$this->witch->url([ 'structure' => $acceptedItem ]).'">'.$acceptedItem.'</a>';
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
if( $require['max'] ?? false ): ?>
    <li>
        <div>Maximum allowed</div>
        <div><?=$require['max'] ?? 0?></div>
    </li>
<?php endif; ?>
