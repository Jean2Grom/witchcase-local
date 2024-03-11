<?php /** @var WC\Module $this */

use WC\DataAccess\Cauldron as CauldronDA;


$conf = [
    'id' => [
        "id_4" => 
        [
            "id" => 4,
            "entries" => [ "test" => false ],
            "modules" => false,
            "craft" => false,
            "parents" => [ "depth" => "*" ],
            "sisters" => false,
            "children" => [ "depth" => "*" ],
        ]
    ]
];

$this->wc->dump( CauldronDA::cauldronRequest($this->wc, $conf) );

echo $this->wc->caudronDepth;
