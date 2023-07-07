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
        $query  .=  ", `policy`.`fk_witch` AS `policy_fk_witch` ";
        
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
            if( $row['policy_fk_witch'] 
                && $row['policy_fk_witch'] !== $row['id'] 
            ){
                continue;
            }
            
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
    
    
    static function getProfiles( WitchCase $wc, array $conditions=[] )
    {
        $query = "";
        $query  .=  "SELECT  `profile`.`id` AS `profile_id` ";
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
        
        $query  .=  "FROM `user__profile` AS `profile` ";
        $query  .=  "LEFT JOIN `user__rel__connexion__profile` ";
        $query  .=      "ON `user__rel__connexion__profile`.`fk_profile` = `profile`.id ";
        $query  .=  "LEFT JOIN `user__policy` AS `policy` ";
        $query  .=      "ON `policy`.`fk_profile` = `profile`.`id` ";
        $query  .=  "LEFT JOIN `witch` ";
        $query  .=      "ON `witch`.`id` = `policy`.`fk_witch` ";
        
        if( !empty($conditions) )
        {
            $separator = "WHERE ";
            foreach( $conditions as $field => $conditionItem )
            {
                $query .= $separator.$field." = '".$conditionItem."' ";
                $separator = "AND ";
            }
        }
        
        $query  .=  "ORDER BY `profile_site` ASC, `profile_name` ASC ";
        
        $result = $wc->db->multipleRowsQuery($query);
        
        $profilesData = [];
        foreach( $result as $row )
        {
            $userProfileId = $row['profile_id'];
            if( empty($profilesData[ $userProfileId ]) ){
                $profilesData[ $userProfileId ] = [
                    'id'        =>  $userProfileId,
                    'name'      =>  $row['profile_name'],
                    'site'      =>  $row['profile_site'],
                    'policies'  =>  [],
                ];
            }
            
            $userPolicyId = $row['policy_id'];
            if( empty($profilesData[ $userProfileId ]['policies'][ $userPolicyId ]) )
            {
                $position       = false;
                $positionWitch  = false;
                if( !empty($row['id']) )
                {
                    $positionWitch  = Witch::createFromData($wc, $row);
                    $position       = $positionWitch->position;
                }

                $profilesData[ $userProfileId ]['policies'][ $userPolicyId ] = [
                    'id'                =>  $userPolicyId,
                    'module'            => $row['policy_module'],
                    'status'            => $row['policy_status'],
                    'custom_limitation' => $row['policy_custom_limitation'],
                    'position'          => $position,
                    'position_rules'    => [
                        'ancestors'         => (boolean) $row['policy_position_ancestors'],
                        'self'              => (boolean) $row['policy_position_included'],
                        'descendants'       => (boolean) $row['policy_position_descendants'],
                    ],
                    'positionName'      => $positionWitch->name ?? '',
                    'positionId'        => $positionWitch->id ?? '',
                ];
            }
        }
                
        return $profilesData;
    }
    
    static function insertProfile( WitchCase $wc, string $name, string $site )
    {
        if( empty($name) || empty($site) ){
            return false;
        }
        
        $query = "";
        $query  .=  "INSERT INTO user__profile (name, site) ";
        $query  .=  "VALUES ( :name, :site ) ";     
        
        return $wc->db->insertQuery($query, [ 'name' => $name, 'site' => $site ]);
    }
    
    static function insertPolicies( WitchCase $wc, int $profileId, array $data )
    {
        if( empty($profileId) || empty($data) ){
            return false;
        }
        
        $query = "";
        $query  .=  "INSERT INTO `user__policy` ";
        $query  .=  "(`fk_profile` ";
        $query  .=  ", `module` ";
        $query  .=  ", `status` ";
        $query  .=  ", `fk_witch` ";
        $query  .=  ", `position_ancestors` ";
        $query  .=  ", `position_included` ";
        $query  .=  ", `position_descendants` ";
        $query  .=  ", `custom_limitation` ";
        $query  .=  ") ";
        
        $query  .=  "VALUES ";
        $query  .=  "( :profile_id ";
        $query  .=  ", :module ";
        $query  .=  ", :status ";
        $query  .=  ", :fk_witch ";
        $query  .=  ", :position_ancestors ";
        $query  .=  ", :position_included ";
        $query  .=  ", :position_descendants ";
        $query  .=  ", :custom_limitation ";
        $query  .=  ") ";
        
        $params = [];
        foreach( $data as $policyData )
        {
            $policyParams = [ 'profile_id' => $profileId ];
            foreach( $policyData as $policyField => $policyFieldValue )
            {
                if( $policyField == 'module' ){
                    $policyParams['module'] = $policyFieldValue;
                }
                elseif( $policyField == 'status' ){
                    $policyParams[ 'status' ] = ($policyFieldValue  != '*')? $policyFieldValue: null;
                }
                elseif( $policyField == 'witch' ){
                    $policyParams['fk_witch'] = $policyFieldValue;
                }
                elseif( $policyField == 'custom' ){
                    $policyParams['custom_limitation'] = $policyFieldValue;
                }
                elseif( $policyField == 'witchRules' )
                {
                    $policyParams['position_ancestors']     = $policyFieldValue["ancestors"]? 1: 0;
                    $policyParams['position_included']      = $policyFieldValue["self"]? 1: 0;
                    $policyParams['position_descendants']   = $policyFieldValue["descendants"]? 1: 0;
                }
            }
            $params[] = $policyParams;
        }
        
        return $wc->db->insertQuery($query, $params, true);
    }
    
    static function updateProfile( WitchCase $wc, int $profileId, array $data )
    {
        if( empty($profileId) || empty($data['name']) || empty($data['site']) ){
            return false;
        }
        
        $query = "";
        $query  .=  "UPDATE `user__profile` ";
        
        $separator  = "SET ";
        $params     = [ 'id' => $profileId ];
        foreach( ['name', 'site'] as $field ){
            if( !empty($data[ $field ]) )
            {
                $query  .=  $separator." `".$field."` = :".$field." ";
                $params[ $field ] = $data[ $field ];
                $separator  = ", ";                
            }
        }
        
        $query  .=  "WHERE `id` = :id ";
        
        return $wc->db->updateQuery($query, $params);
    }
    
    
}
