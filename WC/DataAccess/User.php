<?php

namespace WC\DataAccess;

use WC\WitchCase;
use WC\Witch;
use WC\Website\WitchSummoning;


class User
{    
    static function getUserLoginData( WitchCase $wc, string $login )
    {
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

        $query  .=  "WHERE ( email= :login OR login= :login ) ";
        
        $result = $wc->db->multipleRowsQuery($query, [ 'login' => $login ]);
        
        $userConnexionData = [];
        foreach( $result as $row )
        {
            $userConnexionId = $row['connexion_id'];
            if( empty($userConnexionData[ $userConnexionId ]) )
            {
                $targetColumn =     $row['connexion_attribute_name'];
                $targetColumn .=    '@'.$row['connexion_target_attribute'];
                $targetColumn .=    '#'.$row['connexion_target_attribute_var'];
                
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
        
        $query  .=  "WHERE user_profile.name = :profile ";
        
        $result = $wc->db->multipleRowsQuery($query, [ 'profile' => $profile ]);

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
