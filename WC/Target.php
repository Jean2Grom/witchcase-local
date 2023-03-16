<?php

namespace WC;

use WC\WitchCase;
use WC\DataTypes\ExtendedDateTime;
use WC\DataTypes\Signature;
use WC\Attribute;

class Target 
{
    const ELEMENTS          = [
        "id"        => "INT(11) UNSIGNED NOT NULL AUTO_INCREMENT",
        "name"      => "VARCHAR(255) DEFAULT NULL",
    ];
    
    static $dbFields    =   [
        "`id` int(11) unsigned NOT NULL AUTO_INCREMENT",
        "`name` varchar(255) DEFAULT NULL",
        "`creator` int(11) DEFAULT NULL",
        "`created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP",
        "`modificator` int(11) DEFAULT NULL",
        "`modified` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP",
    ];
    
    static $primaryDbField = "PRIMARY KEY (`id`) ";
    
    var $exist;
    var $attributes;
    var $id;
    var $name;

    /** @var WitchCase */
    var $wc;
    
    /** @var TargetStructure */
    var $structure;
    
    function __construct( WitchCase $wc, TargetStructure $structure, array $data=null )
    {
        $this->wc           = $wc;
        $this->exist        = false;
        $this->attributes   = [];
        
        if( !empty($data) )
        {
            foreach( array_keys(self::ELEMENTS) as $field ){
                if( isset($data[ $field ]) ){
                    $this->{$field} = $data[ $field ];
                }
            }
            
            foreach( $structure->attributes as $attributeName => $attributeData )
            {
                $className = $attributeData["class"];
                $attribute = new $className( $this->wc, $attributeName );
                $attribute->set($data[ $attributeName ]);
                
                $this->attributes[ $attributeName ] = $attribute;
            }
            
            $this->exist = true;
        }
        
        $this->structure    = $structure;
    }
    
    function save()
    {
        foreach( $this->attributes as $attribute ){
            $attribute->save( $this );
        }
        
        $query = "";
        $query  .=  "UPDATE `".$this->wc->db->escape_string($this->structure->table)."` ";
        $query  .=  "SET `name` = '".$this->wc->db->escape_string($this->name)."' ";
        if( !empty($this->wc->user->id) ){
            $query  .=  ", `modificator` = ".$this->wc->user->id." ";
        }
        
        foreach( $this->attributes as $attribute ){
            foreach( $attribute->tableColumns as $key => $tableColumn  )
            {
                $value  =   $this->wc->db->escape_string($attribute->values[ $key ]);
                $query  .=  ", `".$tableColumn."` = '".$value."'";
            }
        }
        
        $query  .=  " WHERE `id` = ".$this->id." ";
        
        return $this->wc->db->updateQuery($query);
    }
    
    
    function fetchTarget( $id, $datatypes )
    {
        $multipleResults = false;
        
        $query  =   "SELECT target.*";
        foreach( $datatypes['Signature'] as $column ){
            $query  .=  ", ".$column.".name AS ".$column."__signature";
        }
        
        foreach( $this->attributes as $attribute ){
            foreach( $attribute->joinFields as $field ){
                $query  .=  ", ".$field['field']." AS `".$field['alias']."`";
            }
        }
        
        $query  .=  " ";
        
        $query  .=  "FROM `".$this->wc->db->escape_string($this->table)."` AS target";
        
        foreach( $this->attributes as $attribute ){
            foreach( $attribute->leftJoin as $leftJoin )
            {
                $multipleResults = true;
                
                $query  .=  " LEFT JOIN `".$leftJoin['table']."` ";
                $query  .=  "AS `".$leftJoin['alias']."` ";
                $query  .=  "ON ".$leftJoin['condition'];
            }
        }
        
        foreach( $datatypes['Signature'] as $column ){
            $query  .=  ", `user_connexion` AS ".$column;
        }
        
        foreach( $this->attributes as $attribute ){
            foreach( $attribute->joinTables as $table ){
                $query  .=  ", `".$table['table']."` AS `".$table['alias']."`";
            }
        }
        
        $query  .=  " ";
        
        $query  .=  "WHERE target.id = '".$this->wc->db->escape_string($id)."' ";
        foreach( $datatypes['Signature'] as $column ){
            $query  .=  "AND ".$column.".id = target.".$column." ";
        }
        
        foreach( $this->attributes as $attribute ){
            foreach( $attribute->joinConditions as $condition ){
                $query  .=  "AND ".$condition." ";
            }
        }
        
        $groupByArray = [];
        foreach( $this->attributes as $attribute ){
            foreach( $attribute->groupBy as $groupBy ){
                $groupByArray[] =  $groupBy;
            }
        }
        
        if( count($groupByArray) > 0 ){
            $query .= " GROUP BY ".implode(", ", $groupByArray);
        }
        
        if( $multipleResults )
        {
            $result     = $this->wc->db->multipleRowsQuery($query);
            
            if( empty($result) )
            {
                $this->wc->debug->dump($query, "Query doesn't get any results");
                return false;
            }
            
            $targetData = $result[0];
            
            foreach( $targetData as $resultColumn => $unused )
            {
                $columnData = Attribute::splitColumn($resultColumn);
                
                if( !$columnData ){
                    continue;
                }
                
                foreach( $this->attributes as $attribute )
                {
                    if( count($attribute->leftJoin) > 0 )
                    {
                        if( strcmp($attribute->type, $columnData['type']) == 0
                            && strcmp($attribute->name, $columnData['name']) == 0
                            && !in_array($resultColumn, $attribute->tableColumns) 
                        ){
                            $groupByArray = [];
                            foreach( $attribute->groupBy as $groupByColumn ){
                                $groupByArray[trim($groupByColumn, '`')] = [];
                            }
                            
                            $data = [];
                            foreach( $result as $resultItem )
                            {
                                $getData = true;
                                foreach( $attribute->groupBy as $groupByColumn )
                                {
                                    $groupByColumn = trim($groupByColumn, '`');
                                    if( in_array($resultItem[$groupByColumn], $groupByArray[$groupByColumn]) )
                                    {
                                        $getData = false;
                                        break;
                                    }
                                    else {
                                        $groupByArray[$groupByColumn][] = $resultItem[$groupByColumn];
                                    }
                                }
                                
                                if( $getData ){
                                    $data[] = $resultItem[$resultColumn];
                                }
                            }
                            
                            $targetData[$resultColumn] = $data;
                        }
                    }   
                }
            }
        }
        else
        {
            $targetData = $this->wc->db->singleRowQuery($query);
            
            if( !is_array($targetData) )
            {
                $this->wc->debug->dump($query, "Query doesn't get any results");
                return false;
            }
        }
        
        if( $targetData ){
            return $this->set($targetData);
        }
        else {
            return false;
        }
    }
    
    protected function setTarget( $args, $datatypes )
    {
        $doneAttributes = [];
        foreach( $args as $argLabel => $argValue )
        {
            if( strcmp("@", substr($argLabel, 0, 1)) != 0 )
            {
                if( in_array($argLabel, $datatypes['ExtendedDateTime']) ){
                    $this->$argLabel = new ExtendedDateTime($argValue);
                }
                elseif( in_array($argLabel, $datatypes['Signature']) )
                {
                    $this->$argLabel =  new Signature(  $argLabel, 
                                                        $argValue, 
                                                        $args[$argLabel."__signature"] 
                                        );
                    
                    unset($args[$argLabel."__signature"]);
                }
                elseif( strcmp("context", $argLabel) == 0 ){
                    $context = $this->formatContext($argValue);
                }
                else {
                    $this->$argLabel = $argValue;
                }
            }
            else
            {
                $columnData = Attribute::splitColumn($argLabel);
                
                if( !isset($this->attributes[ $columnData['name'] ]) ){
                    continue;
                }
                elseif( !in_array($columnData['name'], $doneAttributes) )
                {
                    $doneAttributes[] = $columnData['name'];
                    $this->attributes[ $columnData['name'] ]->set($args);
                }
            }
        }
        
        return true;
    }
    
    function edit( $args )
    {
        foreach( $this->attributes as $attribute )
        {
            $attribute->set( $args );
            $attribute->save( $this );
        }
        
        $query  =   "UPDATE `".$this->wc->db->escape_string($this->table)."` SET ";
        
        if( strcmp($this->type, 'archive')  != 0 )
        {
            /*
            $currentDate    = date("Y-m-d H:i:s");
            $userID         = $_SESSION[$this->wc->website->name]["user"]["connexionID"];
            
            $this->modificator          = $userID; 
            $this->modification_date    = new ExtendedDateTime($currentDate);
            
            $query  .=  "`modificator` = '".$userID."', ";
            $query  .=  "`modification_date` = '".$this->modification_date->sqlFormat()."', ";
             * 
             */
        }
        
        $first = true;
        foreach( $this->attributes as $attribute ){
            foreach( $attribute->tableColumns as $key => $tableColumn  ){
                if( strcmp($attribute->values[$key], "__last_value__") != 0 )
                {
                    $value  =   $this->wc->db->escape_string($attribute->values[$key]);

                    if( $first ){
                        $first = false;
                    }
                    else {
                        $query  .=  ", ";
                    }
                    
                    $query  .=  "`".$tableColumn."` = '".$value."'";
                }
            }
        }
        
        $query  .=  " WHERE `id` = '".$this->wc->db->escape_string($this->id)."' ";
        
        return $this->wc->db->updateQuery($query);
    }
    
    
    function formatContext( $contextString )
    {
        if( !$contextString ){
            return false;
        }
        
        $items = explode(",", $contextString);
        
        $this->context = array();
        foreach( $items as $item )
        {
            if( strstr($item, ":") === false ){
                continue;
            }
            
            $buffer = explode( ":", trim($item) );
            $this->context[trim($buffer[0])] = trim($buffer[1]);
        }
        
        return $this->context;
    }
    
}
