<?php
namespace WC;

class Police 
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
    
    static function createFromData(  Profile $profile, array $data )
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
        $police->statusLabel       = $data['statusLabel'] ?? '*';
        
        
        $profile->wc->dump($profile);
        $profile->wc->dump($police);
        return $police;
    }

    static function insert( WitchCase $wc, int $profileID, array $data )
    {
        $query = "";
        $query  .=  "INSERT INTO `user_profile_policy` ";
        $query  .=  "(`fk_user_profile` ";
        $query  .=  ", `module` ";
        $query  .=  ", `status` ";
        $query  .=  ", `fk_witch` ";
        $query  .=  ", `position_ancestors` ";
        $query  .=  ", `position_included` ";
        $query  .=  ", `position_descendants` ";
        $query  .=  ", `custom_limitation` ";
        $query  .=  ") ";

        $separator = "VALUES ";
        foreach( $data['policies'] as $policyData )
        {
            $query  .=  $separator;
            $query  .=  "( ".$profileID;
            $query  .=  ", \"".( $policyData['module'] ?? '*' )."\" ";
            $query  .=  ", ".( $policyData['status'] ?? 'NULL' )." ";
            $query  .=  ", ".( $policyData['witchId'] ?? 'NULL' );
            $query  .=  ", ".($policyData['parents']? 1: 0);
            $query  .=  ", ".($policyData['included']? 1: 0);
            $query  .=  ", ".($policyData['children']? 1: 0);
            $query  .=  ", \"".($policyData['custom_limitation'] ?? "")."\" ";
            $query  .=  ") ";
            $separator = ", ";
        }
        
        $wc->db->insertQuery($query);
        
        return true;
    }
        
    
    function formArray()
    {
        if( !isset($this->localisation) )
        {
            if( !isset($this->position) ){
                $this->position = explode(',', $this->positionString);
            }
            
            $localisationPosition   = [];
            $this->inherit_subtree  = false;
            foreach( $this->position as $i => $levelValue ){
                if( strcmp($levelValue, '*') == 0 ){
                    $this->inherit_subtree = true;
                }
                else {
                    $localisationPosition[$i+1] = $levelValue;
                }
            }
            
            //$this->localisation = Localisation::getFromPosition( $this->wc, $localisationPosition );
        }
        
        $return =   array(
                        "id"            => $this->id,
                        "module"        => $this->module." - ".$this->action,
                        "localisation"  => $this->localisation,
                        "inherit"       => $this->inherit_subtree,
                        "limitation"    => $this->rigths_limitation,
                        "inherit"       => $this->inherit_subtree,
                    );
        
        return $return;
    }
}
