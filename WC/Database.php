<?php
namespace WC;

use WC\Database\PDO;
use WC\Database\MySQLi;

class Database
{
    /** @var WitchCase */
    var $wc;
    var $ressource;
    
    var $mysqli;
    
    function __construct( WitchCase $wc )
    {
        $this->wc   = $wc;
        $parameters = $this->wc->configuration->read('database');
        
        if( $parameters['driver'] === "mysqli"){        
            $this->ressource    =   new MySQLi($this->wc, $parameters);            
        }
        else {
            $this->ressource    = new PDO( $this->wc, $parameters );
        }        
    }
    
    
    function fetchQuery( string $query, array $bindParams=[] )
    {
        return $this->ressource->fetchQuery($query, $bindParams);
    }
    
    function multipleRowsQuery( string $query, array $bindParams=[] )
    {
        return $this->selectQuery( $query, $bindParams );        
    }
    
    function countQuery( string $query, array $bindParams=[] )
    {
        $result = $this->fetchQuery($query, $bindParams);
        
        if( !is_array($result) || count($result) !== 1 ){
            return false;
        }
        
        return array_values($result)[0] ?? false;
    }
    
    function selectQuery( string $query, array $bindParams=[] )
    {
        return $this->ressource->selectQuery($query, $bindParams);   
    }
    
    function insertQuery( string $query, array $bindParams=[], $multiple=false )
    {
        return $this->ressource->insertQuery($query, $bindParams, $multiple); 
    }
    
    function updateQuery( $query, $bindParams=false ){
        return $this->mysqli->query($query, $bindParams);
    }
    
    function deleteQuery( $query, $bindParams=false ){
        return $this->mysqli->query($query, $bindParams);
    }
    
    function alterQuery( $query, $bindParams=false ){
        return $this->mysqli->query($query, $bindParams);
    }
    
    function createQuery( $query, $bindParams=false ){
        return $this->mysqli->query($query, $bindParams);
    }
    
    function escape_string( string $string ): string
    {
        return $this->ressource->escape_string( $string );
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