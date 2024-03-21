<?php /** @var WC\Module $this */

use WC\DataAccess\Cauldron as CauldronDA;
use WC\Handler\CauldronHandler;

$conf = [
    'user' => [
        "parents"   => [ "depth" => "1" ],
        "siblings"  => [ "depth" => "*" ],
        "children"  => [ "depth" => "*" ],
    ],
    
    'id' => [
        [
            "id" => 7,
            //"parents" => [ "depth" => 5 ],
            "parents" => false,
            "children" => [ "depth" => "*" ],
        ]
    ],
    
];


$result = CauldronHandler::fetch($this->wc, $conf);

$this->wc->dump( $result );

/*
$result = CauldronDA::cauldronRequest($this->wc, $conf);

foreach( $result as $row )
{
    $cauldron = CauldronHandler::createFromData($this->wc, $row);
    
$this->wc->dump( $cauldron );
}
*/


echo $this->wc->caudronDepth;
