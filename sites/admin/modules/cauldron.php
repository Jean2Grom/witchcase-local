<?php /** @var WC\Module $this */

use WC\DataAccess\Cauldron as CauldronDA;


$conf = [
    'id' => [
        "id_4" => 
        [
            "id" => 4,
            "entries" => 
            [ "test" => "test" ],
        ]
    ]
];

echo $this->wc->caudronDepth;
