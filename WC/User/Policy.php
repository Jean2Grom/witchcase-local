<?php
namespace WC\User;

use WC\WoodWiccan;
use WC\Website;

/**
 * Class to handle a single security access policy
 * 
 * @author Jean2Grom
 */
class Policy 
{
    public $id;
    public $module;
    public $position;
    public $position_rules;
    public $custom_limitation;
    public $status;
    public $positionName;
    public $positionId;
    public $statusLabel;
    
    /** 
     * Class to handle security access profiles
     * @var Profile
     */
    public Profile $profile;
    
    /** 
     * WoodWiccan container class to allow whole access to Kernel
     * @var WoodWiccan
     */
    public WoodWiccan $wc;
    
    function __construct( WoodWiccan $wc, Profile $profile )
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
