<?php
namespace WC;

class Database
{
    /** @var WitchCase */
    var $wc;
    var $mysqli;
    
    function __construct( WitchCase $wc )
    {
        $this->wc   = $wc;
        $parameters = $this->wc->configuration->read('database');
        
        if( !empty($parameters['port']) ){
            $this->mysqli   =   new \mysqli(   
                $parameters['server'], 
                $parameters['user'], 
                $parameters['password'], 
                $parameters['database'],
                $parameters['port']
            );
        }
        else {
            $this->mysqli   =   new \mysqli(   
                $parameters['server'], 
                $parameters['user'], 
                $parameters['password'], 
                $parameters['database']
            );
        }

        if( !empty($parameters['charset']) ){
            $this->mysqli->set_charset( $parameters['charset'] );
        }
        
        if( $this->mysqli->connect_error ){
            $this->wc->log->error(    
                'Database connexion failed (' .$this->mysqli->connect_errno . ') '.$this->mysqli->connect_error, 
                true
            );
        }
    }
    
    function singleRowQuery( $query, $bindParams=false )
    {
        $result = $this->query($query, $bindParams);
        
        if( !$result ){
            return false;
        }
        
        if( $result->num_rows == 0 ){
            $return  = 0;
        }
        elseif( $result->num_rows > 1 ){
            $return = $result->num_rows;
        }
        else {
            $return = $result->fetch_assoc();
        }
        
        $result->free();
        
        return $return;
    }
    
    function multipleRowsQuery( $query, $bindParams=false )
    {
        $result = $this->query($query, $bindParams);
        
        if( !$result ){
            return false;
        }

        if( $result->num_rows == 0 ){
            return [];
        }

        $rows = [];
        while( $row = $result->fetch_assoc() ){
            $rows[] = $row;
        }

        $result->free();

        return $rows;        
    }
    
    private function query( $query, $bindParams=false )
    {
        if( $bindParams !== false )
        {
            $stmt = $this->mysqli->prepare($query);
            
            if( !is_array($bindParams) ){
                $bindParams = [ $bindParams ];
            }

            $stmt->bind_param(self::getMysqliParamType($bindParams), ...$bindParams);
            $stmt->execute();
            return $stmt->get_result();
        }
        
        return $this->mysqli->query( $query );
    }
    
    private static function getMysqliParamType($params) 
    {
        $returnTypeString = "";
        foreach( $params as $param ){
            if( ctype_digit($param) ){
                $returnTypeString .= "i";
            }
            elseif( is_numeric($param) ){
                $returnTypeString .= "d";
            }
            else {
                $returnTypeString .= "s"; 
            }
        }
        
        return $returnTypeString;
    }
            
    function countQuery( $query, $bindParams=false )
    {
        $result = $this->query($query, $bindParams);
        
        if( !$result )
        {
            $result->free();
            return false;
        }
        
        $rows = $result->fetch_assoc();
        
        foreach( $rows as $value ){
            $rowCount = $value;
        }
        
        $result->free();
        
        return $rowCount;
    }
    
    function selectQuery( $query ){
        return $this->multipleRowsQuery($query);
    }
    
    function insertQuery( $query, $bindParams=false )
    {
        $result = $this->query($query, $bindParams);
        
        if( !$result ){
            return false;
        }
        
        return $this->mysqli->insert_id;
    }
    
    function updateQuery( $query, $bindParams=false ){
        return $this->query($query, $bindParams);
    }
    
    function deleteQuery( $query, $bindParams=false ){
        return $this->query($query, $bindParams);
    }
    
    function alterQuery( $query, $bindParams=false ){
        return $this->query($query, $bindParams);
    }
    
    function createQuery( $query, $bindParams=false ){
        return $this->query($query, $bindParams);
    }
    
    function escape_string( $string ){
        return $this->mysqli->real_escape_string( $string );
    }
    
    function begin(){
        return $this->mysqli->query( "BEGIN" );
    }
    
    function savePoint( $savePointName ){
        return $this->mysqli->query( "SAVEPOINT ".$this->escape_string($savePointName) );
    }
    
    function rollback( $savePointName = false )
    {
        if( $savePointName )
        {
            $result = $this->mysqli->query( "ROLLBACK TO ".$this->escape_string($savePointName) );
            
            if( $result ){
                return $result;
            }
        }
        
        return $this->mysqli->query( "ROLLBACK" );
    }
    
    function commit(){
        return $this->mysqli->query( "COMMIT" );
    }
    
    function errno(){
        return $this->mysqli->errno;
    }
}