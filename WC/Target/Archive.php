<?php
namespace WC\Target;

use WC\Target;


class Archive extends Target 
{
    const TYPE      = 'archive';
    const DB_FIELDS = [
        "`content_key` int(11) DEFAULT NULL",
    ];
    const ELEMENTS = [ 
        'content_key',
    ];

    var $content_key;
    
       
    function archive( bool $historyMode=false )
    {
        return false;
    }
}
