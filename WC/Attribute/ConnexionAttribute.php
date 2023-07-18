<?php
namespace WC\Attribute;

use WC\Attribute;
use WC\WitchCase;
use WC\Craft;
use WC\DataAccess\User;

class ConnexionAttribute extends Attribute 
{
    const ATTRIBUTE_TYPE    = "connexion";
    const ELEMENTS          = [
        "id" => "INT(11) DEFAULT NULL",
    ];
    const PARAMETERS        = [];
    
    var $password;
    var $password_confirm;
    
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
                'table'     =>  "user__connexion",
                'condition' =>  ":user__connexion.`id` = :craft_table.`".$this->name."@connexion#id`",
            ],
            [
                'table'     =>  "user__rel__connexion__profile",
                'condition' =>  ":user__rel__connexion__profile.`fk_connexion` = :user__connexion.`id`",
            ],
        ];
        
        $this->joinFields   =   [
            'name'          =>  ":user__connexion.`name` AS :craft_table|".$this->name."#name`",
            'login'         =>  ":user__connexion.`login` AS :craft_table|".$this->name."#login`",
            'email'         =>  ":user__connexion.`email` AS :craft_table|".$this->name."#email`",
            'pass_hash'     =>  ":user__connexion.`pass_hash` AS :craft_table|".$this->name."#pass_hash`",
            'profile_id'    =>  ":user__rel__connexion__profile.`fk_profile` AS :craft_table|".$this->name."#profile_id`",
        ];
    }
    
    function getEditParams(): array
    {
        return [
            self::getColumnName( $this->type, $this->name, 'id' ),
            self::getColumnName( $this->type, $this->name, 'name' ),
            self::getColumnName( $this->type, $this->name, 'login' ),
            self::getColumnName( $this->type, $this->name, 'email' ),
            self::getColumnName( $this->type, $this->name, 'password' ),
            self::getColumnName( $this->type, $this->name, 'password_confirm' ),
            [
                'name'      => self::getColumnName( $this->type, $this->name, 'profiles' ), 
                'option'    => FILTER_REQUIRE_ARRAY 
            ],
        ];
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
        if( in_array($key, array_keys($this->values) ) ){
            $this->values[ $key ] = $value;
        }
        
        if( $key == 'password' ){
            $this->password = $value;
        }
        elseif( $key == 'password_confirm' ){
            $this->password_confirm = $value;
        }
        
        if( $this->password && $this->password_confirm )
        {
            if( $this->password !== $this->password_confirm ){
                $this->wc->user->addAlerts([[
                    'level'     =>  'warning',
                    'message'   =>  "Password mismatch"
                ]]);
            }
            else 
            {
                $this->values[ 'pass_hash' ] = $this->generate_hash( $this->password );
                $this->wc->user->addAlerts([[
                    'level'     =>  'success',
                    'message'   =>  "Password changed"
                ]]);
            }
        }

        
        
        if( $key == 'idXXX' )
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
            $query  .=  "FROM `user__connexion` ";
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
        
        
        return $this;
    }
    
    function save( $craft ) 
    {
        if( empty($this->values['id']) )
        {
            $craftAttributeData = [
                'table' => $craft->structure->table,
                'type'  => $this->type,
                'var'   => 'id',
                'name'  => $this->name,
            ];
            
            $this->values['id'] = User::insertConnexion( $this->wc, $this->values, $craftAttributeData );
        }
        else
        {
            $query = "";            
            $query  .=  "UPDATE `user__connexion` ";
            $query  .=  "SET `name` = '".$this->wc->db->escape_string($this->values['name'])."' ";
            $query  .=  ", `email` = '".$this->wc->db->escape_string($this->values['email'])."' ";
            $query  .=  ", `login` = '".$this->wc->db->escape_string($this->values['login'])."' ";
            $query  .=  ", `pass_hash` = '".$this->wc->db->escape_string($this->values['pass_hash'] ?? "")."' ";
            $query  .=  ", `craft_table` = '".$this->wc->db->escape_string($craft->structure->table)."' ";
            $query  .=  ", `craft_attribute` = '".$this->wc->db->escape_string($this->type)."' ";
            $query  .=  ", `craft_attribute_var` = 'id' ";
            $query  .=  ", `attribute_name` = '".$this->wc->db->escape_string($this->name)."' ";
            if( !empty($this->wc->user->id) ){
                $query  .=  ", `modifier` = '".$this->wc->user->id."' ";
            }
            
            $query  .=  "WHERE `id` = '".$this->wc->db->escape_string($this->values['id'])."' ";
            
            $this->wc->db->updateQuery($query);
            
            $query = "";
            $query  .=  "DELETE FROM `user__rel__connexion__profile` ";
            $query  .=  "WHERE `fk_connexion` = '".$this->wc->db->escape_string($this->values['id'])."' ";
            
            $this->wc->db->deleteQuery($query);
        }
        
        if( !empty($this->values['profiles']) )
        {
            $query = "";
            $query  .=  "INSERT INTO `user__rel__connexion__profile` ";
            $query  .=  "( `fk_connexion`, `fk_profile`) ";
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
    
    function clone( Craft $craft )
    {
        $clonedAttribute = clone $this;
        
        unset($clonedAttribute->values['id']);
        
        return $clonedAttribute;
    }

    
}