<?php
namespace WC;

use WC\Database\PDO;
use WC\Database\MySQLi;

class Database
{
    /** @var WitchCase */
    var $wc;
    var $ressource;
    
    function __construct( WitchCase $wc )
    {
        $this->wc   = $wc;
        $parameters = $this->wc->configuration->read( 'database' );
        
        if( $parameters['driver'] === "mysqli"){        
            $this->ressource    =   new MySQLi( $this->wc, $parameters );            
        }
        else {
            $this->ressource    = new PDO( $this->wc, $parameters );
        }        
    }
    
    function fetchQuery( string $query, array $bindParams=[] )
    {
        return $this->ressource->fetchQuery( $query, $bindParams );
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
        return $this->ressource->selectQuery( $query, $bindParams );   
    }
    
    function insertQuery( string $query, array $bindParams=[], $multiple=false )
    {
        return $this->ressource->insertQuery( $query, $bindParams, $multiple ); 
    }
    
    function query( string $query, array $bindParams=[], $multiple=false ){
        return $this->ressource->query( $query, $bindParams, $multiple );
    }
    
    function updateQuery( string $query, array $bindParams=[], $multiple=false ){
        return $this->ressource->query( $query, $bindParams, $multiple );
    }
    
    function deleteQuery( string $query, array $bindParams=[], $multiple=false ){
        return $this->ressource->query( $query, $bindParams, $multiple );
    }
    
    function alterQuery( string $query, array $bindParams=[], $multiple=false ){
        return $this->ressource->query( $query, $bindParams, $multiple );
    }
    
    function createQuery( string $query, array $bindParams=[], $multiple=false ){
        return $this->ressource->query( $query, $bindParams, $multiple );
    }
    
    function escape_string( string $string ): string
    {
        return $this->ressource->escape_string( $string );
    }
    
    function begin(){
        return $this->ressource->query( "BEGIN" );
    }
    
    function savePoint( string $savePointName ){
        return $this->ressource->query( "SAVEPOINT :savePointName ", ['savePointName' => $savePointName] );
    }
    
    function rollback( string $savePointName='' )
    {
        if( !empty($savePointName) )
        {
            $result = $this->ressource->query( "ROLLBACK TO :savePointName ", ['savePointName' => $savePointName] );
            
            if( $result ){
                return $result;
            }
        }
        
        return $this->ressource->query( "ROLLBACK" );
    }
    
    function commit(){
        return $this->ressource->query( "COMMIT" );
    }
    
    function errno(){
        return $this->ressource->errno();
    }
    
    function debugQuery( string $query, array $params=[] )
    {
        $paramsKeys     = [];
        $paramsValues   = [];
        
        foreach( $params as $key => $value )
        {
            if( str_starts_with( $key, ':' ) ){
                $paramsKeys[] = $key;
            }
            else {
                $paramsKeys[] = ':'.$key;
            }
            
            $paramsValues[] = '"'.$value.'"';
        }
        
        $caller = debug_backtrace()[1];
        
        return $this->wc->debug->dump( 
            str_replace($paramsKeys, $paramsValues, $query), 
            'DEBUG SQL QUERY', 
            1, 
            [
                'file' => $caller['file'], 
                'line' => $caller['line']
            ] 
        );
    }
}