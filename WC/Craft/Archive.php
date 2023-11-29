<?php
namespace WC\Craft;

use WC\Craft;

/**
 * class to handle Archived crafts
 * 
 * @author Jean2Grom
 */
class Archive extends Craft 
{
    const TYPE      = 'archive';
    const DB_FIELDS = [
        "`content_key` int(11) DEFAULT NULL",
    ];
    const ELEMENTS = [ 
        'content_key',
    ];

    public $content_key;
    
       
    function archive( bool $historyMode=false )
    {
        return false;
    }
}
