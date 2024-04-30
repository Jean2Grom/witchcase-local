<?php /** @var WC\Module $this */

use WC\DataAccess\CauldronDataAccess;
use WC\Handler\CauldronHandler;

$conf = [
    'user',
    7,    
];

$this->wc->dump( $conf );

$result = CauldronHandler::fetch($this->wc, $conf);

$this->wc->dump( $result );

/*
$result = CauldronDataAccess::cauldronRequest($this->wc, $conf);

foreach( $result as $row )
{
    $cauldron = CauldronHandler::createFromData($this->wc, $row);
    
$this->wc->dump( $cauldron );
}
*/


echo $this->wc->cauldronDepth;
