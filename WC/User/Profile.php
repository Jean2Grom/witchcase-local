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
        $profiles = self::listProfiles( $wc, [ '`profile`.`id`' => $id ] );
        
        return $profiles[ $id ] ?? false;
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
        $this->wc->db->begin();
        try {
            if( !empty($this->policies) ){
                UserDA::deletePolicies( $this->wc, array_keys($this->policies) );
            }
            
            UserDA::deleteProfile( $this->wc, $this->id );
        } 
        catch( \Exception $e ) 
        {
            $this->wc->log->error($e->getMessage());
            $this->wc->db->rollback();
            return false;
        }
        $this->wc->db->commit();
        
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
    
    
    static function createNew( WitchCase $wc, array $newProfileData=[] )
    {
        if( empty($newProfileData['name']) || empty($newProfileData['site']) ){
            return false;
        }
        
        $wc->db->begin();
        try {
            $profileId = UserDA::insertProfile($wc, $newProfileData['name'], $newProfileData['site']);
            
            if( !empty($newProfileData['policies']) ){
                UserDA::insertPolicies($wc, $profileId, $newProfileData['policies']);
            }
        } 
        catch( \Exception $e ) 
        {
            $wc->log->error($e->getMessage());
            $wc->db->rollback();
            return false;
        }
        $wc->db->commit();
        
        return $profileId;
    }
    
    
    static function edit( WitchCase $wc, array $profileData=[] )
    {
        if( empty($profileData['id']) ){
            return false;
        }
        
        $wc->db->begin();
        try {
            UserDA::updateProfile( $wc, $profileData['id'], $profileData );
            
            if( !empty($profileData["policiesToDelete"]) ){
                UserDA::deletePolicies( $wc, $profileData["policiesToDelete"] );
            }
            
            $newPolicies        = [];
            $updatedPolicies    = [];
            foreach( $profileData["policies"] as $policyData ){
                if( !empty($policyData['id']) ){
                    $updatedPolicies[] = $policyData;
                }
                else {
                    $newPolicies[] = $policyData;
                }
            }
            
            if( !empty($newPolicies) ){
                UserDA::insertPolicies($wc, $profileData['id'], $newPolicies);
            }
            
            if( !empty($updatedPolicies) ){
                UserDA::updatePolicies($wc, $profileData['id'], $updatedPolicies);
            }
        } 
        catch( \Exception $e ) 
        {
            $wc->log->error($e->getMessage());
            $wc->db->rollback();
            return false;
        }
        $wc->db->commit();
        
        return true;
    }
}
