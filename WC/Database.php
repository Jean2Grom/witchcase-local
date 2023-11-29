<?php
namespace WC;

use WC\Database\PDO;
use WC\Database\MySQLi;

/**
 * Class handeling Database connexions and requesting
 * 
 * @author Jean2Grom
 */
class Database
{
    public $ressource;
    
    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;
    
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
        $this->wc->debug->databaseAnalysePrepare( 'FETCH' );
        $result = $this->ressource->fetchQuery( $query, $bindParams );        
        $this->wc->debug->databaseAnalyse( 'FETCH' );
        
        return $result;
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
        $this->wc->debug->databaseAnalysePrepare( 'SELECT' );
        $result = $this->ressource->selectQuery( $query, $bindParams );
        $this->wc->debug->databaseAnalyse( 'SELECT' );
        
        return $result;
    }
    
    function insertQuery( string $query, array $bindParams=[], $multiple=false )
    {
        $this->wc->debug->databaseAnalysePrepare( 'INSERT' );
        $result = $this->ressource->insertQuery( $query, $bindParams, $multiple );         
        $this->wc->debug->databaseAnalyse( 'INSERT' );
        
        return $result;
    }
    
    function query( string $query, array $bindParams=[], $multiple=false )
    {
        $this->wc->debug->databaseAnalysePrepare( 'QUERY' );
        $result = $this->ressource->query( $query, $bindParams, $multiple );
        $this->wc->debug->databaseAnalyse( 'QUERY' );
        
        return $result;
    }
    
    function updateQuery( string $query, array $bindParams=[], $multiple=false )
    {
        $this->wc->debug->databaseAnalysePrepare( 'UPDATE' );
        $result = $this->ressource->query( $query, $bindParams, $multiple );
        $this->wc->debug->databaseAnalyse( 'UPDATE' );
        
        return $result;
    }
    
    function deleteQuery( string $query, array $bindParams=[], $multiple=false )
    {
        $this->wc->debug->databaseAnalysePrepare( 'DELETE' );
        $result = $this->ressource->query( $query, $bindParams, $multiple );
        $this->wc->debug->databaseAnalyse( 'DELETE' );
        
        return $result;
    }
    
    function alterQuery( string $query, array $bindParams=[], $multiple=false )
    {
        $this->wc->debug->databaseAnalysePrepare( 'ALTER' );
        $result = $this->ressource->query( $query, $bindParams, $multiple );
        $this->wc->debug->databaseAnalyse( 'ALTER' );
        
        return $result;
    }
    
    function createQuery( string $query, array $bindParams=[], $multiple=false ){
        try {
            $this->wc->debug->databaseAnalysePrepare( 'CREATE' );
            $result = $this->ressource->query( $query, $bindParams, $multiple );
            $this->wc->debug->databaseAnalyse( 'CREATE' );

            return $result;
        }
        catch( \Exception $e ){
            return false;
        }
    }
    
    function escape_string( string $string ): string
    {
        return $this->ressource->escape_string( $string );
    }
    
    function begin(){
        return $this->ressource->query( "START TRANSACTION" );
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
        
    function debugQuery( string $query, array $params=[] )
    {
        $paramsKeys     = [];
        $paramsValues   = [];
        
        $replaceParams  = ( count($params) > 0 && is_array( array_values($params)[0] ) )?  array_values($params)[0]: $params;
        $replaceParamsBuffer = [];
        foreach( $replaceParams as $key => $value )
        {
            $length                         = strlen($key);            
            $replaceParamsBuffer[ $length ] = array_merge($replaceParamsBuffer[ $length ] ?? [], [ $key => $value ]);            
        }
        
        krsort($replaceParamsBuffer);
        
        foreach( $replaceParamsBuffer as $orderedReplaceParams ){
            foreach( $orderedReplaceParams as $key => $value )
            {
                if( str_starts_with( $key, ':' ) ){
                    $paramsKeys[] = $key;
                }
                else {
                    $paramsKeys[] = ':'.$key;
                }

                $paramsValues[] = '"'.$value.'"';
            }
        }   
        
        $caller = debug_backtrace()[0];
        
        return $this->wc->debug->dump( 
            str_replace($paramsKeys, $paramsValues, $query), 
            'DEBUG SQL QUERY', 
            1, 
            [
                'file' => $caller['file'], 
                'line' => $caller['line']
            ] 
        ) && $this->wc->debug->dump( 
            $params, 
            'DEBUG SQL PARAMS',
            2, 
            [
                'file' => $caller['file'], 
                'line' => $caller['line']
            ] 
        );
    }
}