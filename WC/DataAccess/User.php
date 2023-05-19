<?php
namespace WC\DataAccess;

use WC\WitchCase;
use WC\Witch;
use WC\Cairn;

class User
{    
    static function getUserLoginData( WitchCase $wc, string $login )
    {
        if( empty($login) ){
            return [];
        }
        
        $query = "";
        $query  .=  "SELECT `user__connexion`.`id` AS `connexion_id` ";
        $query  .=  ", `user__connexion`.`name` AS `connexion_name` ";
        $query  .=  ", `user__connexion`.`email` AS `connexion_email` ";
        $query  .=  ", `user__connexion`.`login` AS `connexion_login` ";
        $query  .=  ", `user__connexion`.`pass_hash` AS `connexion_pass_hash` ";
        $query  .=  ", `user__connexion`.`craft_table` AS `connexion_craft_table` ";
        $query  .=  ", `user__connexion`.`craft_attribute` AS `connexion_craft_attribute` ";
        $query  .=  ", `user__connexion`.`craft_attribute_var` AS `connexion_craft_attribute_var` ";
        $query  .=  ", `user__connexion`.`attribute_name` AS `connexion_attribute_name` ";
        
        $query  .=  ", `profile`.`id` AS `profile_id` ";
        $query  .=  ", `profile`.`name` AS `profile_name` ";
        $query  .=  ", `profile`.`site` AS `profile_site` ";
        
        $query  .=  ", `policy`.`id` AS `policy_id` ";
        $query  .=  ", `policy`.`module` AS `policy_module` ";
        $query  .=  ", `policy`.`status` AS `policy_status` ";
        $query  .=  ", `policy`.`position_ancestors` AS `policy_position_ancestors` ";
        $query  .=  ", `policy`.`position_included` AS `policy_position_included` ";
        $query  .=  ", `policy`.`position_descendants` AS `policy_position_descendants` ";
        $query  .=  ", `policy`.`custom_limitation` AS `policy_custom_limitation` ";
        
        foreach( Witch::FIELDS as $field ){
            $query      .=  ", `witch`.`".$field."` ";
        }
        for( $i=1; $i<=$wc->depth; $i++ ){
            $query      .=  ", `witch`.`level_".$i."` ";
        }
        
        $query  .=  "FROM `user__connexion` ";
        $query  .=  "LEFT JOIN `user__rel__connexion__profile` ";
        $query  .=      "ON `user__rel__connexion__profile`.`fk_connexion` = `user__connexion`.`id` ";
        $query  .=  "LEFT JOIN `user__profile` AS `profile` ";
        $query  .=      "ON `profile`.`id` = `user__rel__connexion__profile`.`fk_profile` ";
        $query  .=  "LEFT JOIN `user__policy` AS `policy` ";
        $query  .=      "ON `policy`.`fk_profile` = `profile`.`id` ";
        $query  .=  "LEFT JOIN `witch` ";
        $query  .=      "ON `witch`.`id` = `policy`.`fk_witch` ";

        $query  .=  "WHERE ( `email`= :login OR `login`= :login ) ";
        
        $result = $wc->db->multipleRowsQuery($query, [ 'login' => $login ]);
        
        $userConnexionData = [];
        foreach( $result as $row )
        {
            $userConnexionId = $row['connexion_id'];
            if( empty($userConnexionData[ $userConnexionId ]) )
            {
                $craftColumn =     $row['connexion_attribute_name'];
                $craftColumn .=    '@'.$row['connexion_craft_attribute'];
                $craftColumn .=    '#'.$row['connexion_craft_attribute_var'];
                
                $userConnexionData[ $userConnexionId ] = [
                    'id'            => $userConnexionId,
                    'name'          => $row['connexion_name'],
                    'email'         => $row['connexion_email'],
                    'login'         => $row['connexion_login'],
                    'pass_hash'     => $row['connexion_pass_hash'],
                    'craft_table'  => $row['connexion_craft_table'],
                    'craft_column' => $craftColumn,
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
                
        $witches        = WitchSummoning::summon($wc, Cairn::prepareConfiguration($wc->website, $configuration) );
        
        $wc->user->connexionData    = $savedConnexionData;
        $wc->user->connexion        = $savedConnexionValue;
        
        return $witches['target'] ?? false;
    }
    
    
    static function getPublicProfileData(  WitchCase $wc, string $profile )
    {
        $query = "";
        $query  .=  "SELECT `user__profile`.`id` AS `profile_id` ";
        $query  .=  ", `user__profile`.`name` AS `profile_name` ";

        $query  .=  ", `policy`.`id` AS `policy_id` ";
        $query  .=  ", `policy`.`module` AS `policy_module` ";
        $query  .=  ", `policy`.`status` AS `policy_status` ";
        $query  .=  ", `policy`.`position_ancestors` AS `policy_position_ancestors` ";
        $query  .=  ", `policy`.`position_included` AS `policy_position_included` ";
        $query  .=  ", `policy`.`position_descendants` AS `policy_position_descendants` ";
        $query  .=  ", `policy`.`custom_limitation` AS `policy_custom_limitation` ";

        $query  .=  ", `witch`.* ";

        $query  .=  "FROM `user__profile` ";
        $query  .=  "LEFT JOIN `user__policy` AS `policy` ";
        $query  .=      "ON `policy`.`fk_profile` = `user__profile`.`id` ";
        $query  .=  "LEFT JOIN `witch` ";
        $query  .=      "ON `witch`.`id` = `policy`.`fk_witch` ";
        
        $query  .=  "WHERE `user__profile`.`name` = :profile ";
        
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
