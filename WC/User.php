<?php

namespace WC;

use WC\Website\WitchSummoning;
use WC\DataAccess\User as UserDA;

class User 
{
    var $id;
    var $name;
    var $profiles;
    var $policies;
    var $connexion      = false;
    var $connexionData  = false;
    var $loginMessages  = [];
    
    /** @var Session */
    var $session;
    
    /** @var WitchCase */
    var $wc;
    
    function __construct( WitchCase $wc )
    {
        $this->wc           = $wc;
        $this->connexion    = false;
        $this->profiles     = [];
        $this->policies     = [];
        $this->session      = new Session($this->wc);
        
        // If previous page is login page
        if( $this->wc->request->param('login') === 'login' )
        {
            $loginFailure       = false;
            $userName           = $this->wc->request->param('username');
            $userConnexionData  = UserDA::getUserLoginData( $this->wc, $userName );
            
            if( count($userConnexionData) == 0 )
            {
                $loginFailure           = true;
                $this->loginMessages[]  = "Unknon username";
                $this->wc->debug->dump('Login failed : unknown username');
            }
            elseif( count($userConnexionData) > 1 ) 
            {
                $loginFailure           = true;
                $this->loginMessages[]  = "Problem whith this username: multiple match ";
                $this->loginMessages[]  = "Please contact administrator";
                $this->wc->log->error('Login failed : multiple username match');
            }
            
            if( !$loginFailure )
            {
                $connexionData  = array_values($userConnexionData)[0];                
                $hash           = crypt( $this->wc->request->param('password'), $connexionData['pass_hash'] );
                
                if( $hash !== $connexionData['pass_hash'] )
                {
                    $loginFailure           = true;
                    $this->loginMessages[]  = "Wrong password, please try again";
                    $this->wc->debug->dump('Login failed : wrong password for login: '.$userName);
                }                
            }
            
            if( !$loginFailure )
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
                
                $this->session->write(
                    'user', 
                    [
                        'connexionID'   => $this->id,
                        'name'          => $this->name,
                        'profiles'      => $this->profiles,
                        'policies'      => $this->policies,
                        'connexionData' => $this->connexionData,
                    ]
                );
            }
        }
        
        // Get last connexion 
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
            
            $getPublicProfileData = UserDA::getPublicProfileData($wc, $publicProfile);

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
        $userConnexionData = UserDA::getUserLoginData( $this->wc, $login );
        
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
}
