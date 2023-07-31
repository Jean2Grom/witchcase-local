<?php
namespace WC;

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
        $this->session      = new Session($this->wc);
        
        $this->connexion    = false;
        $this->id           = 0;
        $this->name         = '';
        $this->profiles     = [];
        $this->policies     = [];
        
        // If previous page is login page
        if( $this->wc->request->param('action') === 'login' )
        {
            $loginFailure       = false;
            $userName           = $this->wc->request->param('username');
            $userConnexionData  = UserDA::getUserLoginData( $this->wc, $userName );
            
            if( count($userConnexionData) == 0 )
            {
                $loginFailure           = true;
                $this->loginMessages[]  = "Unknown username";
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
                
                if( !password_verify( $this->wc->request->param('password'), $connexionData['pass_hash'] ) )
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
        $sessionData = $this->session->read('user');
        if( !$this->connexion && $sessionData )
        {
            $this->profiles         = $sessionData['profiles'];
            $this->policies         = $sessionData['policies'];
            $this->id               = $sessionData['connexionID'] ?? false;
            $this->name             = $sessionData['name'] ?? array_values($this->profiles)[0] ?? '';
            $this->connexionData    = $sessionData['connexionData'] ?? false;
            $this->connexion        = (bool) ($this->id);
        }
        elseif( !$this->connexion ) // No user log in, get default user (="public user") from configuration
        {
            $this->name     = $this->wc->configuration->read('system', 'publicUser') ?? "Public";
            $publicProfile  = $this->wc->configuration->read('system', 'publicUserProfile') ?? 'public';
            
            $getPublicProfileData = UserDA::getPublicProfileData($wc, $publicProfile);

            $this->profiles = $getPublicProfileData['profiles'];
            $this->policies = $getPublicProfileData['policies'];
            
            $this->session->write(
                'user', 
                [
                    'name'          => $this->name,
                    'profiles'      => $this->profiles,
                    'policies'      => $this->policies,
                    'connexionID'   => false,
                    'connexionData' => false,
                ]
            );            
        }
        
        if( empty($this->policies) )
        {
            $this->session->destroy();
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
            $this->loginMessages[] = "Unknown username";
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
        
        return true;
    }
    
    function disconnect()
    {
        $this->session->destroy();
        $this->connexion = false;
        
        return $this;
    }
    
    function getAlerts(): array
    {
        $alerts = $this->session->read('alerts');
        $this->session->delete('alerts');
        
        if( !$alerts ){
            return [];
        }
        
        return $alerts;
    }
    
    function addAlerts( array $newAlerts ): self
    {
        foreach( $newAlerts as $newAlertItem ){
            $this->session->pushTo('alerts', $newAlertItem);
        }
        
        return $this;
    }
    
    function getSessionData( string $varname ){
        return $this->session->read( $varname );
    }
    
    function setSessionData( string $varname, mixed $varvalue )
    {
        $this->session->write( $varname, $varvalue );
        
        return $this;
    }
    
    function addToSessionData( string $varname, mixed $varvalue )
    {
        $this->session->pushTo( $varname, $varvalue );
        
        return $this;
    }    
}
