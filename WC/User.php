<?php

namespace WC;

use WC\Website\WitchSummoning;

class User 
{
    var $id;
    var $name;
    var $profiles;
    var $policies;
    var $connexion      = false;
    var $connexionData  = false;
    var $loginMessages  = [];
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc )
    {
        $this->wc           = $wc;
        $this->connexion    = false;
        $this->profiles     = [];
        $this->policies     = [];
        
        // If previous page is login page
        if( filter_has_var(INPUT_POST, 'login') 
                && strcmp(filter_input(INPUT_POST, 'login'), 'login') == 0 )
        {
            $userName = filter_input(INPUT_POST, 'username');
            
            $userConnexionData = self::getUserLoginData( $this->wc, $userName );
            
            if( count($userConnexionData) == 0 )
            {
                $this->loginMessages[] = "Unknon username";
                $this->wc->debug->dump('Login failed : unknown username');
            }
            elseif( count($userConnexionData) > 1 ) 
            {
                $this->loginMessages[] = "Problem whith this username: multiple match ";
                $this->loginMessages[] = "Please contact administrator";
                $this->wc->log->error('Login failed : multiple username match');
            }
            else
            {
                $connexionData = array_values($userConnexionData)[0];
                
                $hash = crypt( filter_input(INPUT_POST, 'password'), $connexionData['pass_hash'] );
                
                if( $hash !== $connexionData['pass_hash'] )
                {
                    $this->loginMessages[]  = "Wrong password, please try again";
                    $this->wc->debug->dump('Login failed : wrong password for login: '.$userName);
                }
                else
                {
                    $this->connexion        = true;
                    $this->profiles         = $connexionData['profiles'];
                    $this->id               = $connexionData['id'];
                    $this->name             = $connexionData['name'];
                    $this->connexionData    = $connexionData;
                    
                    foreach( $connexionData['profiles'] as $profileData ){
                        foreach( $profileData['policies'] as $policyId => $policyData ){
                            if( empty($this->policies[ $policyId ]) ){
                                $this->policies[ $policyId ] = $policyData;
                            }
                        }
                    }
                    
                    $_SESSION[$this->wc->website->name]['user']   =   [
                        'connexionID'   => $this->id,
                        'name'          => $this->name,
                        'profiles'      => $this->profiles,
                        'policies'      => $this->policies,
                        'connexionData' => $this->connexionData,
                    ];
                }
            }
        }
        
        
        if( !$this->connexion && isset($_SESSION[$this->wc->website->name]['user']) )
        {
            $this->profiles         = $_SESSION[$this->wc->website->name]['user']['profiles'];
            $this->policies         = $_SESSION[$this->wc->website->name]['user']['policies'];
            $this->id               = $_SESSION[$this->wc->website->name]['user']['connexionID'] ?? false;
            $this->name             = $_SESSION[$this->wc->website->name]['user']['name'] ?? array_values($this->profiles)[0] ?? '';
            $this->connexionData    = $_SESSION[$this->wc->website->name]['user']['connexionData'] ?? false;
            $this->connexion        = (bool) ($this->id);
        }
        elseif( !$this->connexion ) // No user log in, get default user (="public user") from configuration
        {
            $this->name     = $this->wc->configuration->read('system', 'publicUser') ?? "Public";
            $publicProfile  = $this->wc->configuration->read('system', 'publicUserProfile') ?? 'public';
            
            $getPublicProfileData = self::getPublicProfileData($wc, $publicProfile);

            $this->profiles = $getPublicProfileData['profiles'];
            $this->policies = $getPublicProfileData['policies'];
            
            $_SESSION[$this->wc->website->name]['user'] = [
                'name'          => $this->name,
                'profiles'      => $this->profiles,
                'policies'      => $this->policies,
                'connexionID'   => false,
                'connexionData' => false,
            ];
        }
        
        if( empty($this->policies) )
        {
            session_destroy();
            $this->loginMessages[] = "Problem whith this system: unable to log user";
            $this->loginMessages[] = "Please contact administrator";
            $this->wc->log->error('Login failed : accessing policies impossible', true);
        }
    }
    
    function connectTo( $login )
    {
        $userConnexionData = self::getUserLoginData( $this->wc, $login );
        
        if( count($userConnexionData) == 0 )
        {
            $this->loginMessages[] = "Unknon username";
            $this->wc->debug->dump('Login failed : unknown username');
            return false;
        }
        elseif( count($userConnexionData) > 1 ) 
        {
            $this->loginMessages[] = "Problem whith this username: multiple match ";
            $this->loginMessages[] = "Please contact administrator";
            $this->wc->log->error('Login failed : multiple username match');
            
            return false;
        }
        
        $connexionData = array_values($userConnexionData)[0];
        
        $this->connexion        = true;
        $this->profiles         = $connexionData['profiles'];
        $this->id               = $connexionData['id'];
        $this->name             = $connexionData['name'];
        $this->connexionData    = $connexionData;
        
        foreach( $connexionData['profiles'] as $profileData ){
            foreach( $profileData['policies'] as $policyId => $policyData ){
                if( empty($this->policies[ $policyId ]) ){
                    $this->policies[ $policyId ] = $policyData;
                }
            }
        }
        
        $_SESSION[$this->wc->website->name]['user']   =   [
            'connexionID'   => $this->id,
            'name'          => $this->name,
            'profiles'      => $this->profiles,
            'policies'      => $this->policies,
            'connexionData' => $this->connexionData,
        ];
        
        return true;
    }
    
    function disconnect()
    {
        session_destroy();
        
        $this->connexion = false;
        
        return $this;
    }
    
    function getAlerts()
    {
        $alerts = [];
        if( !empty($_SESSION[$this->wc->website->name]['alerts']) )
        {
            $alerts = $_SESSION[$this->wc->website->name]['alerts'];
            $_SESSION[$this->wc->website->name]['alerts'] = [];
        }
        
        return $alerts;
    }
    
    function addAlerts( $newAlerts )
    {
        $alerts = $this->getAlerts();
        
        foreach( $newAlerts as $newAlertItem ){
            $alerts[] = $newAlertItem;
        }
        
        $_SESSION[$this->wc->website->name]['alerts'] = $alerts;
        
        return $this;
    }
    
    function getSessionData( $varname )
    {
        return $_SESSION[$this->wc->website->name][ $varname ] ?? NULL;
    }
    
    function setSessionData( $varname, $varvalue )
    {
        if( empty($_SESSION[ $this->wc->website->name ]) ){
            $_SESSION[ $this->wc->website->name ] = [];
        }
        
        $_SESSION[$this->wc->website->name][ $varname ] = $varvalue;
        
        return $this;
    }
    
    function addToSessionData( $varname, $varvalue )
    {
        if( empty($_SESSION[ $this->wc->website->name ]) ){
            $_SESSION[ $this->wc->website->name ] = [];
        }
        
        if( empty($_SESSION[ $this->wc->website->name ][ $varname ]) ){
            $_SESSION[ $this->wc->website->name ][ $varname ] = [];
        }
        
        $_SESSION[$this->wc->website->name][ $varname ][] = $varvalue;
        
        return $this;
    }
    
    static function getUserLoginData( WitchCase $wc, $username )
    {
        $login = $wc->db->escape_string( $username );
        
        $query = "";
        $query  .=  "SELECT user_connexion.id AS connexion_id ";
        $query  .=  ", user_connexion.name AS connexion_name ";
        $query  .=  ", user_connexion.email AS connexion_email ";
        $query  .=  ", user_connexion.login AS connexion_login ";
        $query  .=  ", user_connexion.pass_hash AS connexion_pass_hash ";
        $query  .=  ", user_connexion.target_table AS connexion_target_table ";
        $query  .=  ", user_connexion.target_attribute AS connexion_target_attribute ";
        $query  .=  ", user_connexion.target_attribute_var AS connexion_target_attribute_var ";
        $query  .=  ", user_connexion.attribute_name AS connexion_attribute_name ";
        
        $query  .=  ", profile.id AS profile_id ";
        $query  .=  ", profile.name AS profile_name ";
        $query  .=  ", profile.site AS profile_site ";
        
        $query  .=  ", policy.id AS policy_id ";
        $query  .=  ", policy.module AS policy_module ";
        $query  .=  ", policy.status AS policy_status ";
        $query  .=  ", policy.position_ancestors AS policy_position_ancestors ";
        $query  .=  ", policy.position_included AS policy_position_included ";
        $query  .=  ", policy.position_descendants AS policy_position_descendants ";
        $query  .=  ", policy.custom_limitation AS policy_custom_limitation ";
        
        foreach( Witch::FIELDS as $field ){
            $query      .=  ", witch.".$field." ";
        }
        for( $i=1; $i<=$wc->website->depth; $i++ ){
            $query      .=  ", witch.level_".$i." ";
        }
        
        $query  .=  "FROM user_connexion ";
        $query  .=  "LEFT JOIN rel__user_connexion__user_profile ";
        $query  .=      "ON rel__user_connexion__user_profile.fk_user_connexion = user_connexion.id ";
        $query  .=  "LEFT JOIN user_profile AS profile ";
        $query  .=      "ON profile.id = rel__user_connexion__user_profile.fk_user_profile ";
        $query  .=  "LEFT JOIN user_profile_policy AS policy ";
        $query  .=      "ON policy.fk_user_profile = profile.id ";
        $query  .=  "LEFT JOIN witch ";
        $query  .=      "ON witch.id = policy.fk_witch ";

        $query  .=  "WHERE ( email= ? OR login= ? ) ";

        $result = $wc->db->multipleRowsQuery($query, [ $login, $login ]);
        
        $userConnexionData = [];
        foreach( $result as $row )
        {
            $userConnexionId = $row['connexion_id'];
            if( empty($userConnexionData[ $userConnexionId ]) )
            {
                $targetColumn = '@_'.$row['connexion_target_attribute'];
                if( !empty($row['connexion_target_attribute_var']) ){
                    $targetColumn .= '#'.$row['connexion_target_attribute_var'];
                }
                $targetColumn .= '__'.$row['connexion_attribute_name'];

                $userConnexionData[ $userConnexionId ] = [
                    'id'            => $userConnexionId,
                    'name'          => $row['connexion_name'],
                    'email'         => $row['connexion_email'],
                    'login'         => $row['connexion_login'],
                    'pass_hash'     => $row['connexion_pass_hash'],
                    'target_table'  => $row['connexion_target_table'],
                    'target_column' => $targetColumn,
                    'profiles'      => [],
                ];
            }

            $userProfileId = $row['profile_id'];
            if( empty($userConnexionData[ $userConnexionId ]['profiles'][ $userProfileId ]) ){
                $userConnexionData[ $userConnexionId ]['profiles'][ $userProfileId ] = [
                    'id'        =>  $userProfileId,
                    'name'      =>  $row['profile_name'],
                    'policies'  =>  [],
                ];
            }

            $userPolicyId = $row['policy_id'];
            if( empty($userConnexionData[ $userConnexionId ]['profiles'][ $userProfileId ]['policies'][ $userPolicyId ]) )
            {
                $position = false;
                if( !empty($row['id']) )
                {
                    $positionWitch  = Witch::createFromData($wc, $row);
                    $position       = $positionWitch->position;
                }

                $userConnexionData[ $userConnexionId ]['profiles'][ $userProfileId ]['policies'][ $userPolicyId ] = [
                    'id'                =>  $userPolicyId,
                    'module'            => $row['policy_module'],
                    'status'            => $row['policy_status'] ?? '*',
                    'custom_limitation' => $row['policy_custom_limitation'],
                    'position'          => $position,
                    'position_rules'    => [
                        'ancestors'         => (boolean) $row['policy_position_ancestors'],
                        'self'              => (boolean) $row['policy_position_included'],
                        'descendants'       => (boolean) $row['policy_position_descendants'],
                    ],
                ];
            }
        }
        
        return $userConnexionData;
    }
    
    static function getUserWitchFromConnexionData( WitchCase $wc, $connexionData) 
    {
        $savedConnexionData     = $wc->user->connexionData ?? [];
        $savedConnexionValue    = $wc->user->connexion ?? false;
        
        $wc->user->connexionData    = $connexionData;
        $wc->user->connexion        = 1;
        
        $configuration = [
            'target' => [
                'user'  => true,
                'craft' => true,
            ]
        ];
        
        $witchSummoning = new WitchSummoning( $wc, $configuration, $wc->website );
        $witches        = $witchSummoning->summon();
        
        $wc->user->connexionData    = $savedConnexionData;
        $wc->user->connexion        = $savedConnexionValue;
        
        return $witches['target'] ?? false;
    }
    
    
    static function getPublicProfileData(  WitchCase $wc, string $profile )
    {
        $query = "";
        $query  .=  "SELECT user_profile.id AS profile_id ";
        $query  .=  ", user_profile.name AS profile_name ";

        $query  .=  ", policy.id AS policy_id ";
        $query  .=  ", policy.module AS policy_module ";
        $query  .=  ", policy.status AS policy_status ";
        $query  .=  ", policy.position_ancestors AS policy_position_ancestors ";
        $query  .=  ", policy.position_included AS policy_position_included ";
        $query  .=  ", policy.position_descendants AS policy_position_descendants ";
        $query  .=  ", policy.custom_limitation AS policy_custom_limitation ";

        $query  .=  ", witch.* ";

        $query  .=  "FROM user_profile ";
        $query  .=  "LEFT JOIN user_profile_policy AS policy ";
        $query  .=      "ON policy.fk_user_profile = user_profile.id ";
        $query  .=  "LEFT JOIN witch ";
        $query  .=      "ON witch.id = policy.fk_witch ";
        
        $query  .=  "WHERE user_profile.name = ? ";
        
        $result = $wc->db->multipleRowsQuery($query, $profile);

        $profiles   = [];
        $policies   = [];
        foreach( $result as $row )
        {
            if( empty($profiles[ $row['profile_id'] ]) ){
                $profiles[ $row['profile_id'] ] = $row['profile_name'];
            }
            
            if( empty($policies[ $row['policy_id'] ]) )
            {
                $position = false;
                if( !empty($row['id']) )
                {
                    $positionWitch  = Witch::createFromData($wc, $row);
                    $position       = $positionWitch->position;
                }

                $policies[ $row['policy_id'] ] = [
                    'module'            => $row['policy_module'],
                    'status'            => $row['policy_status'],
                    'custom_limitation' => $row['policy_custom_limitation'],
                    'position'          => $position,
                    'position_rules'    => [
                        'ancestors'         => (boolean) $row['policy_position_ancestors'],
                        'self'              => (boolean) $row['policy_position_included'],
                        'descendants'       => (boolean) $row['policy_position_descendants'],
                    ],
                ];
            }
        }
        
        return [ 
            'profiles' => $profiles, 
            'policies' => $policies 
        ];
    }
}
