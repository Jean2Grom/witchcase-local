<?php
namespace WC\Database;

use WC\WitchCase;

class PDO 
{
    /** @var WitchCase */
    var $wc;
    var $pdo;
    
    function __construct( WitchCase $wc, $parameters )
    {
        $this->wc   = $wc;
        
        $dsn    =   $parameters['driver'] ?? "mysql";
        $dsn    .=  ':host='.$parameters['server'];
        $dsn    .=  ';dbname='.$parameters['database'];
        if( $parameters['port'] ){
            $dsn    .=  ';port='.$parameters['port'];            
        }
        if( $parameters['charset'] ){
            $dsn    .=  ';charset='.$parameters['charset'];            
        }
        
        $this->pdo = new \PDO( 
            $dsn,
            $parameters['user'],
            $parameters['password'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );    
    }
    
    function __destruct() {
        $this->pdo = null;
    }
    
    function fetchQuery( string $query, array $bindParams=[] )
    {
        if( empty($bindParams) ){
            $stmt = $this->pdo->query( $query );
        }
        else 
        {
            $stmt = $this->pdo->prepare($query);

            foreach( $bindParams as $bindParamsKey => $bindParamsValue ){
                $stmt->bindValue( $bindParamsKey, $bindParamsValue, self::getParamType($bindParamsValue) );
            }
            
            $stmt->execute();
        }
        
        $count = $stmt->rowCount();
        
        if( $count !== 1 ){
            return $count;
        }
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    function selectQuery( string $query, array $bindParams=[] )
    {
        if( empty($bindParams) ){
            $stmt = $this->pdo->query( $query );
        }
        else 
        {
            $stmt = $this->pdo->prepare($query);

            foreach( $bindParams as $bindParamsKey => $bindParamsValue ){
                $stmt->bindValue( $bindParamsKey, $bindParamsValue, self::getParamType($bindParamsValue) );
            }
            
            $stmt->execute();
        }
                
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    function insertQuery( string $query, array $bindParams=[], $multiple=false )
    {
        if( empty($bindParams) )
        {
            $result = $this->pdo->query( $query );
            
            return $result? $this->pdo->lastInsertId(): false;
        }
        
        $stmt = $this->pdo->prepare($query);
        
        if( !$multiple )
        {
            foreach( $bindParams as $bindParamsKey => $bindParamsValue ){
                $stmt->bindValue( $bindParamsKey, $bindParamsValue, self::getParamType($bindParamsValue) );
            }
            
            return $stmt->execute()? $this->pdo->lastInsertId(): false;
        }
        
        $return = [];
        $bindParamsKeys     = array_keys(array_values($bindParams)[0]);
        $bindParamsValues   = array_flip($bindParamsKeys);
        
        foreach( $bindParamsKeys as $key ){
            $stmt->bindParam( $key, $bindParamsValues[ $key ], self::getParamType(array_values($bindParams)[0][ $key ]) ); 
        }
        
        foreach( $bindParams as $bindParamsItem )
        {
            foreach( $bindParamsKeys as $key ){
                $bindParamsValues[ $key ] = $bindParamsItem[ $key ];
            }
            
            $return[] = $stmt->execute()? $this->pdo->lastInsertId(): false;
        }
        
        return $return;
    }
    
    private static function getParamType($param) 
    {
        if( is_int($param) ){
            return \PDO::PARAM_INT;
        }
        elseif( is_null($param) || $param === 'NULL' || $param === 'null' ){
            return \PDO::PARAM_NULL;
        }
        
        return \PDO::PARAM_STR;
    }    
    
    function escape_string( string $string ): string
    {
        return htmlspecialchars($string);
    }
    

}