<?php /** @var WC\Module $this */

use WC\DataAccess\Cauldron as CauldronDA;


$conf = [
    'user' => [
        "parents"   => [ "depth" => "1" ],
        "sisters"   => [ "depth" => "*" ],
        "children"  => [ "depth" => "*" ],
    ],
    
    'id' => [
        "id_7" => 
        [
            "id" => 7,
            "entries" => [ "test" => false ],
            //"parents" => [ "depth" => "1" ],
            "parents" => false,
            //"sisters" => [ "depth" => "*" ],
            "children" => [ "depth" => "*" ],
        ]
    ]
];

$this->wc->dump( CauldronDA::cauldronRequest($this->wc, $conf) );

echo $this->wc->caudronDepth;
