<?php
namespace WC\User;

use WC\WitchCase;
use WC\Website;

class Policy 
{
    var $id;
    var $module;
    var $position;
    var $position_rules;
    var $custom_limitation;
    var $status;
    var $positionName;
    var $positionId;
    var $statusLabel;
    
    /** @var Profile */
    var $profile;
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc, Profile $profile )
    {
        $this->wc       = $wc;
        $this->profile  = $profile;
    }
    
    static function createFromData(  Profile $profile, array $data, ?Website $website=null )
    {
        $police = new self( $profile->wc, $profile );
        
        $police->id                = $data['id'];
        $police->module            = $data['module'];
        $police->status            = $data['status'] ?? '*';
        $police->position          = $data['position'];
        $police->position_rules    = $data['position_rules'];
        $police->custom_limitation = $data['custom_limitation'];
        $police->positionName      = $data['positionName'];
        $police->positionId        = $data['positionId'];
        
        if( $website )
        {
            $profile->wc->dump( $police->status );
        }  
        
        $police->statusLabel       = $data['statusLabel'] ?? '*';
        
        return $police;
    }
}
