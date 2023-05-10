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
    
    static $datatypes            =   array(
                                            'Signature'         =>  array(
                                                                        'last_modificator', 
                                                                        'archiver'
                                                                    ),
                                            'ExtendedDateTime'  =>  array(
                                                                        'last_modification_date', 
                                                                        'archive_date'
                                                                    )
                                        );
    
    function restore()
    {
        return true;
    }
    
    function publish()
    {
        return $this->restore();
    }
    
    function archive( bool $historyMode=false )
    {
        return false;
    }
}
