<?php /** @var WC\Module $this */

use WC\DataAccess\Cauldron as CauldronDA;


$conf = [
    'user' => [
        "parents" => false,
        "children" => [ "depth" => "*" ],
    ],
    'id' => [
        "id_7" => 
        [
            "id" => 7,
            "entries" => [ "test" => false ],
            "parents" => [ "depth" => "1" ],
            "children" => [ "depth" => "*" ],
        ]
    ]
];

$this->wc->dump( CauldronDA::cauldronRequest($this->wc, $conf) );

echo $this->wc->caudronDepth;
