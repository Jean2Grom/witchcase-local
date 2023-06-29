<?php
namespace WC\User;


use WC\WitchCase;
use WC\DataAccess\User as UserDA;

use WC\Website;

class Profile 
{
    var $id;
    var $name;
    var $site;
    var $policies;
    
    /** @var Witchcase */
    var $wc;
    
    function __construct( WitchCase $wc )
    {
        $this->wc = $wc;
        
        $this->id       = false;
        $this->name     = false;
        $this->policies = [];
    }
    
    static function createFromId( WitchCase $wc, int $id )
    {
        $profiles = self::listProfiles( $wc, [ 'profile_id' => $id ] );
        
        return $profiles[0];
    }
    
    static function createFromData(  WitchCase $wc, array $data )
    {
        $profile = new self( $wc );
        
        $profile->id    = $data['id'];
        $profile->name  = $data['name'];
        $profile->site  = $data['site'];
        
        if( $profile->site == '*' ){
            $statusLabels = $wc->configuration->read("global", "status");
        }
        elseif( $wc->website->name == $profile->site ){
            $statusLabels = $wc->website->status;
        }
        else
        {
            $website = new Website( $wc, $profile->site );
            $statusLabels = $website->status;
        }
        
        foreach( $data['policies'] as $policyData )
        {
            if( !is_null($policyData['status']) && !empty($statusLabels[ $policyData['status'] ]) ){
                $policyData['statusLabel'] = $statusLabels[ $policyData['status'] ];
            }
            
            $police = Policy::createFromData( $profile, $policyData );
            $profile->policies[ $police->id ] = $police;
        }
        
        return $profile;
    }
    
    function delete()
    {
        $this->wc->cache->delete('profiles', $this->name);
        
        $query  =   "DELETE FROM `user__profile` ";
        $query  .=  "WHERE id='".$this->wc->db->escape_string($this->id)."' ";
        
        if( !$this->wc->db->deleteQuery( $query ) )
        {
            $this->wc->log->error("Can't delete profile ".$this->name." with ID ".$this->id);
            return false;
        }
        
        $query  =   "DELETE FROM `user__rel__connexion__profile` ";
        $query  .=  "WHERE `fk_profile`='".$this->wc->db->escape_string($this->id)."' ";
        
        if( !$this->wc->db->deleteQuery( $query ) )
        {
            $this->wc->log->error("Can't delete profile ".$this->name." with ID ".$this->id);
            return false;
        }
        
        $query  =   "DELETE FROM `police` ";
        $query  .=  "WHERE fk_profile='".$this->wc->db->escape_string($this->id)."' ";
        
        if( !$this->wc->db->deleteQuery( $query ) ){
            $this->wc->log->error("Can't delete policies for profile ".$this->name." with ID ".$this->id);
        }
        
        return true;
    }
    
    static function listProfiles( WitchCase $wc, array $conditions=[] )
    {
        $profiles = [];
        foreach( UserDA::getProfiles($wc, $conditions) as $profileDataItem ){
            $profiles[ $profileDataItem['id'] ] = self::createFromData( $wc, $profileDataItem );
        }
        
        return $profiles;
    }
    
    
    static function insert( WitchCase $wc, array $newProfileData=[] )
    {
        if( empty($newProfileData['name']) ){
            return false;
        }
        
        $query = "";
        $query  .=  "INSERT INTO user__profile (name, site) ";
        $query  .=  "VALUES ('".$wc->db->escape_string($newProfileData['name'])."', '".$wc->db->escape_string($newProfileData['site'])."') ";     
        
        $profileID = $wc->db->insertQuery($query);
        
        if( !empty($newProfileData['policies']) ){
            Police::insert($wc, $profileID, $newProfileData);
        }
        
        return $profileID;
    }
    
    
    public function edit( array $profileData=[] )
    {
        if( empty($profileData['name']) ){
            return false;
        }
        
        $this->name = $profileData['name'];
        
        $query = "";
        $query  .=  "UPDATE `user__profile` ";
        $query  .=  "SET `name` = '".$this->wc->db->escape_string($profileData['name'])."' ";
        $query  .=  ", `site` = '".$this->wc->db->escape_string($profileData['site'])."' ";
        $query  .=  "WHERE `id` = ".$this->id." ";
        
        $this->wc->db->updateQuery($query);
        
        $query = "";
        $query  .=  "DELETE FROM `user__policy` ";
        $query  .=  "WHERE `fk_profile` = ".$this->id." ";
        
        $this->wc->db->deleteQuery($query);

        if( !empty($profileData['policies']) ){
            Police::insert($this->wc, $this->id, $profileData);
        }
        
        return $this;
    }
}
