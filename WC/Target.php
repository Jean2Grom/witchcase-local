<?php
namespace WC;

use WC\DataAccess\Target as TargetDA;

use WC\WitchCase;
use WC\Datatype\ExtendedDateTime;
use WC\Datatype\Signature;
use WC\Attribute;

class Target 
{
    const TYPES         = [ 
        'content', 
        'draft', 
        'archive',
    ];
    
    const ELEMENTS      = [
        "id"        => "INT(11) UNSIGNED NOT NULL AUTO_INCREMENT",
        "name"      => "VARCHAR(255) DEFAULT NULL",
    ];
    
    static $dbFields    = [
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
    
    private $relatedTargetsIds = [];
    
    
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
            
            foreach( $structure->attributes() as $attributeName => $attributeData )
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
    
    static function factory( WitchCase $wc, TargetStructure $structure, array $data=null )
    {
        $className  = "WC\\Target\\". ucfirst($structure->type);
        
        return new $className( $wc, $structure, $data );
    }
    
    function attribute( string $attributeName ): ?Attribute
    {
        return $this->attributes[ $attributeName ] ?? null;
    }
    
    function getEditParams(): array
    {
        $searchedParams = [ 'name' ];
        
        foreach( $this->attributes as $attribute ){
            array_push( $searchedParams, ...$attribute->getEditParams() );
        }
        
        return $searchedParams;
    }
    
    function update( array $params )
    {
        if( $params['name'] ){
            $this->name = $params['name'];
        }
        
        foreach( $this->attributes as $attribute ){
            $attribute->update( $params );
        }
        
        $table                                          = $this->structure->table;
        $updatedTargets                                 = $this->wc->website->updatedTargets[ $table ] ?? [];
        $updatedTargets[]                               = $this->id;
        $this->wc->website->updatedTargets[ $table ]    = $updatedTargets;
        
        return $this->save();
    }
    
    function publish(){
        return $this->save();
    }
    
    function countWitches()
    {
        $table = $this->structure->table;
        
        return TargetDA::countWitches($this->wc, $table, $this->id);
    }
    
    function getRelatedTargetsIds( string $type  )
    {
        if( !in_array($type, self::TYPES) ){
            return false;
        }
        
        if( !isset($this->relatedTargetsIds[ $type ]) )
        {
            $table                              = $type.'__'.$this->structure->name;            
            $this->relatedTargetsIds[ $type ]   = TargetDA::getRelatedTargetsIds($this->wc, $table, $this->id);
        }
        
        return $this->relatedTargetsIds[ $type ];
    }
    
    function getWitches()
    {
        $table = $this->structure->table;
        
        $witches = [];
        foreach( TargetDA::getWitches($this->wc, $table, $this->id) ?? [] as $data ){
            $witches[] = Witch::createFromData($this->wc, $data);
        }
        
        return $witches;
    }
    
    function delete( bool $deleteAttributes=true )
    {
        $this->wc->db->begin();
        try {
            if( $deleteAttributes ){
                foreach( $this->attributes as $attribute ){
                    $attribute->delete();
                }
            }
            
            $table  = $this->structure->table;
            
            if( TargetDA::delete($this->wc, $table, $this->id) && isset($this->wc->website->craftedData[ $table ][ $this->id ]) ){
                unset($this->wc->website->craftedData[ $table ][ $this->id ]);
            }
        }
        catch( \Exception $e )
        {
            $this->wc->log->error($e->getMessage());
            $this->wc->db->rollback();
            return false;
        }
        $this->wc->db->commit();
        
        $deletedTargets                                 = $this->wc->website->deletedTargets[ $table ] ?? [];
        $deletedTargets[]                               = $this->id;
        $this->wc->website->deletedTargets[ $table ]    = $deletedTargets;
        
        return true;
    }
    
    function save()
    {
        $this->wc->db->begin();
        try {
            if( !$this->id )
            {
                $contentKey = ($this->content_key ?? false)? $this->content_key: null;
                $this->id   = $this->structure->createTarget( $this->name, $this->structure->type, $contentKey );
            }
            
            $updated = 0;
            foreach( $this->attributes as $attribute ){
                $updated += $attribute->save( $this );
            }

            $fields = [ 'name' => $this->name ];

            foreach( $this->attributes as $attribute ){
                foreach( $attribute->tableColumns as $key => $tableColumn  ){
                    $fields[ $tableColumn ] = $attribute->values[ $key ];
                }
            }
            
            $conditions = [ 'id' => $this->id ];
            
            $updated += TargetDA::update( $this->wc, $this->structure->table, $fields, $conditions );
        }
        catch( \Exception $e )
        {
            $this->wc->log->error($e->getMessage());
            $this->wc->db->rollback();
            return false;
        }        
        $this->wc->db->commit();
        
        return $updated;
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
