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
        "id_7" => 
        [
            "id" => 7,
            //"parents" => [ "depth" => "1" ],
            "parents" => false,
            //"siblings" => [ "depth" => "*" ],
            "children" => [ "depth" => "*" ],
        ]
    ]
];

$result = CauldronDA::cauldronRequest($this->wc, $conf);

$this->wc->dump( $result );

foreach( $result as $row )
{
    $cauldron = CauldronHandler::createFromData($this->wc, $row);
    
$this->wc->dump( $cauldron );
}



echo $this->wc->caudronDepth;
