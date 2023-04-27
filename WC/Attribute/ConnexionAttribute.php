<?php
namespace WC\Attribute;

use WC\Attribute;
use WC\WitchCase;

class ConnexionAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "connexion";
    const ELEMENTS          = [
        "id" => "INT(11) DEFAULT NULL",
    ];
    const PARAMETERS        = [];
    
    function __construct( WitchCase $wc, string $attributeName, array $params=[] )
    {
        parent::__construct( $wc, $attributeName, $params );
        
        $this->values       =   [
            'id'                => null,
            'name'              => "",
            'login'             => "",
            'email'             => "",
            'pass_hash'         => "",
            'profiles'          => [],
        ];
        
        $this->joinTables   =   [
            [
                'table'     =>  "user_connexion",
                'condition' =>  ":user_connexion.`id` = :target_table.`".$this->name."@connexion#id`",
            ],
            [
                'table'     =>  "rel__user_connexion__user_profile",
                'condition' =>  ":rel__user_connexion__user_profile.`fk_user_connexion` = :user_connexion.`id`",
            ],
        ];
        
        $this->joinFields   =   [
            'name'          =>  ":user_connexion.`name` AS :target_table|".$this->name."#name`",
            'login'         =>  ":user_connexion.`login` AS :target_table|".$this->name."#login`",
            'email'         =>  ":user_connexion.`email` AS :target_table|".$this->name."#email`",
            'pass_hash'     =>  ":user_connexion.`pass_hash` AS :target_table|".$this->name."#pass_hash`",
            'profile_id'    =>  ":rel__user_connexion__user_profile.`fk_user_profile` AS :target_table|".$this->name."#profile_id`",
        ];
    }
    
    function getEditParams(): array
    {
        $editParams = array_values($this->tableColumns);
        $postedVarSearchArray = [
            'name',
            'login',
            'email',
            'password',
            'password_confirm',
        ];
        
        foreach( $postedVarSearchArray as $postedVarSearchItem ){
            $editParams[] = self::getColumnName( $this->type, $this->name, $postedVarSearchItem );
        }
        
        return $editParams;
    }    
    
    
    function set( $args )
    {
        foreach( $args as $key => $value ){
            if( $key == 'profile_name' ){
                continue;
            }
            elseif( $key == 'profile_id' && is_array($value) ){
                foreach( $value as $profileId ){
                    if( !in_array($profileId, $this->values['profiles']) ){
                        $this->values['profiles'][] = $profileId;
                    }
                }
            }
            elseif( $key == 'profile_id' ){
                $this->values['profiles'] = [ $value ];
            }
            else {
                $this->values[ $key ] = $value;
            }
        }
        
        return $this;
    }
    
    function setValue( $key, $value )
    {
        if( $key == 'id' )
        {
            $postedVarSearchArray = [
                'name',
                'login',
                'email',
                'password',
                'password_confirm',
            ];
            
            $postedVar = [];
            foreach( $postedVarSearchArray as $var ){
                $postedVar[ $var ] = filter_input(INPUT_POST, self::getColumnName( $this->type, $this->name, $var ));
            }
            
            $query = "";
            $query  .=  "SELECT `id`, `login`, `email` ";
            $query  .=  "FROM `user_connexion` ";
            $query  .=  "WHERE `login` = '".$this->wc->db->escape_string($postedVar['login'])."' ";
            $query  .=  "OR `email` =  '".$this->wc->db->escape_string($postedVar['email'])."' ";
            
            $result = $this->wc->db->selectQuery($query);
            $loginUsed = false;
            $emailUsed = false;
            foreach( $result as $row )
            {
                if( $row['login'] == $postedVar['login'] && $value != $row['id'] ){
                    $loginUsed = true;
                }
                
                if( $row['email'] == $postedVar['email'] && $value != $row['id'] ){
                    $emailUsed = true;
                }
            }
            
            
            if( $loginUsed || $emailUsed )
            {
                if( $loginUsed ){
                    $this->wc->user->addAlerts([[
                        'level'     =>  'error',
                        'message'   =>  "Le login est déjà utilisé"
                    ]]);
                }
                
                if( $emailUsed ){
                    $this->wc->user->addAlerts([[
                        'level'     =>  'error',
                        'message'   =>  "L'email est déjà utilisé"
                    ]]);
                }
                
                return false;
            }
            
            $postedVar['profiles'] = filter_input(INPUT_POST, self::getColumnName( $this->type, $this->name, 'profiles' ), FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
            $postedVar['profiles'] = array_filter($postedVar['profiles']);
            
            foreach([ 'name', 'login', 'email', 'profiles' ] as $simpleValuesEntry ){
                if( $postedVar[ $simpleValuesEntry ] !== false ){
                    $this->values[ $simpleValuesEntry ] = $postedVar[ $simpleValuesEntry ];
                }
            }
            
            if( !empty($postedVar['password']) )
            {
                if( empty($postedVar['password_confirm']) ){
                    $this->wc->user->addAlerts([[
                        'level'     =>  'warning',
                        'message'   =>  "Vous devez confirmer le mot de passe afin que ce dernier soit pris en compte"
                    ]]);
                }
                elseif( $postedVar['password'] != $postedVar['password_confirm'] ){
                    $this->wc->user->addAlerts([[
                        'level'     =>  'warning',
                        'message'   =>  "La confirmation du mot passe n'est pas identique"
                    ]]);
                }
                else 
                {
                    $this->values[ 'pass_hash' ] = $this->generate_hash($postedVar['password']);
                    $this->wc->user->addAlerts([[
                        'level'     =>  'success',
                        'message'   =>  "Le nouveau mot de passe est pris en compte"
                    ]]);
                }
            }
        }
        
        $this->values[ $key ] = $value;
        
        return $this;
    }
    
    function save( $target ) 
    {
        $query = "";
        if( empty($this->values['id']) )
        {
            $query .=   "INSERT INTO `user_connexion` ";
            $query .=   "( `name`, `email`, `login`, `pass_hash`, ";
            $query .=   "`target_table`, `target_attribute`, `target_attribute_var`, ";
            
            if( !empty($this->wc->user->id) ){
                $query .=   "`creator`, `modifier`, ";
            }
            
            $query .=   "`attribute_name`) ";
            
            $query .=   "VALUES ('".$this->wc->db->escape_string($this->values['name'])."' ";
            $query .=   ", '".$this->wc->db->escape_string($this->values['email'])."' ";
            $query .=   ", '".$this->wc->db->escape_string($this->values['login'])."' ";
            $query .=   ", '".$this->values['pass_hash']."' ";
            
            $query .=   ", '".$target->structure->table."' ";
            $query .=   ", '".$this->type."' ";
            $query .=   ", 'id' ";

            if( !empty($this->wc->user->id) ){
                $query .=   ", '".$this->wc->user->id."' ";
                $query .=   ", '".$this->wc->user->id."' ";
            }
            
            $query .=   ", '".$this->wc->db->escape_string($this->name)."') ";
            
            $this->values['id'] = $this->wc->db->insertQuery($query);
        }
        else
        {
            $query  .=  "UPDATE `user_connexion` ";
            $query  .=  "SET `name` = '".$this->wc->db->escape_string($this->values['name'])."' ";
            $query  .=  ", `email` = '".$this->wc->db->escape_string($this->values['email'])."' ";
            $query  .=  ", `login` = '".$this->wc->db->escape_string($this->values['login'])."' ";
            $query  .=  ", `pass_hash` = '".$this->wc->db->escape_string($this->values['pass_hash'])."' ";
            $query  .=  ", `target_table` = '".$this->wc->db->escape_string($target->structure->table)."' ";
            $query  .=  ", `target_attribute` = '".$this->wc->db->escape_string($this->type)."' ";
            $query  .=  ", `target_attribute_var` = 'id' ";
            $query  .=  ", `attribute_name` = '".$this->wc->db->escape_string($this->name)."' ";
            if( !empty($this->wc->user->id) ){
                $query  .=  ", `modifier` = '".$this->wc->user->id."' ";
            }
            
            $query  .=  "WHERE `id` = '".$this->wc->db->escape_string($this->values['id'])."' ";
            
            $this->wc->db->updateQuery($query);
            
            $query = "";
            $query  .=  "DELETE FROM `rel__user_connexion__user_profile` ";
            $query  .=  "WHERE `fk_user_connexion` = '".$this->wc->db->escape_string($this->values['id'])."' ";
            
            $this->wc->db->deleteQuery($query);
        }
        
        if( !empty($this->values['profiles']) )
        {
            $query = "";
            $query  .=  "INSERT INTO `rel__user_connexion__user_profile` ";
            $query  .=  "( `fk_user_connexion`, `fk_user_profile`) ";
            $separator = "VALUES ";
            foreach( $this->values['profiles'] as $profileId )
            {
                $query  .=   $separator;
                $separator = ", ";
                $query  .=   "('".$this->wc->db->escape_string($this->values['id'])."' ";
                $query  .=   ", '".$profileId."' ) ";
            }
            
            $this->wc->db->insertQuery($query);
        }
        
        return 1;
    }
    
    function generate_hash($password, $cost=11)
    {
        /* To generate the salt, first generate enough random bytes. Because
         * base64 returns one character for each 6 bits, the we should generate
         * at least 22*6/8=16.5 bytes, so we generate 17. Then we get the first
         * 22 base64 characters
         */
        $randomBytes = substr( base64_encode(openssl_random_pseudo_bytes( 17 )), 0, 22 );
        /* As blowfish takes a salt with the alphabet ./A-Za-z0-9 we have to
         * replace any '+' in the base64 string with '.'. We don't have to do
         * anything about the '=', as this only occurs when the b64 string is
         * padded, which is always after the first 22 characters.
         */
        $salt = str_replace( "+", ".", $randomBytes );
        /* Next, create a string that will be passed to crypt, containing all
         * of the settings, separated by dollar signs
         */
        $param = '$'.implode(   
            '$',
            [
                "2y", //select the most secure version of blowfish (>=PHP 5.3.7)
                str_pad($cost, 2, "0", STR_PAD_LEFT), //add the cost in two digits
                $salt //add the salt
            ]
        );
        
        //now do the actual hashing
        return crypt( $password, $param );
    }
    
}