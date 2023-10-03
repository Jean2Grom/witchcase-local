<?php
namespace WC;

use WC\DataAccess\Craft as CraftDA;
use WC\DataAccess\WitchCrafting;

use WC\WitchCase;
use WC\Datatype\ExtendedDateTime;
use WC\Craft\Draft;
use WC\Attribute;

class Craft 
{
    const TYPES         = [ 
        'content', 
        'draft', 
        'archive',
    ];
    
    const ELEMENTS      = [
        "id",
        "name",
        "creator",
        "created",
        "modificator",
        "modified",
    ];
    
    const DB_FIELDS    = [
        "`id` int(11) unsigned NOT NULL AUTO_INCREMENT",
        "`name` varchar(255) DEFAULT NULL",
        "`creator` int(11) DEFAULT NULL",
        "`created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP",
        "`modificator` int(11) DEFAULT NULL",
        "`modified` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP",
    ];
    
    const PRIMARY_DB_FIELD  = "PRIMARY KEY (`id`) ";
    
    const JOIN_TABLES       =   [
        [
            'table'     =>  "user__connexion",
            'alias'     =>  "creator_connexion",
            'condition' =>  ":creator_connexion.`id` = :craft_table.`creator`",
        ],
        [
            'table'     =>  "user__connexion",
            'alias'     =>  "modificator_connexion",
            'condition' =>  ":modificator_connexion.`id` = :craft_table.`modificator`",
        ],
    ];
    
    const JOIN_FIELDS       =   [
        'creator_name'      =>  ":creator_connexion.`name` AS :craft_table|creator_name`",
        'modificator_name'  =>  ":modificator_connexion.`name` AS :craft_table|modificator_name`",
    ];
    
    var $exist;
    var $attributes;
    
    var $id;
    var $name;
    var $created;
    var $modified;
    
    private $properties         = [];
    private $relatedCraftsIds   = [];
    
    private $witches;
    
    
    /** @var WitchCase */
    var $wc;
    
    /** @var Structure */
    var $structure;
    
    function __construct( WitchCase $wc, Structure $structure, array $data=null )
    {
        $this->wc           = $wc;
        $this->exist        = false;
        $this->attributes   = [];
        
        if( !empty($data) )
        {
            foreach( $structure->getFields() as $field ){
                $this->properties[$field] = $data[ $field ] ?? null;
            }
            
            if( !empty($this->properties['id']) ){
                $this->id = (int) $this->properties['id'];
            }

            if( !empty($this->properties['name']) ){
                $this->name = $this->properties['name'];
            }
            
            if( property_exists($this, 'content_key') && !empty($this->properties['content_key']) ){
                $this->content_key = $this->properties['content_key'];
            }
            
            if( !empty($data['created']) ){
                $this->created = new ExtendedDateTime( $data['created'], $data['creator_name'] ?? '' );
            }
            
            if( !empty($data['modified']) ){
                $this->modified = new ExtendedDateTime( $data['modified'], $data['modificator_name'] ?? '' );
            }
            
            foreach( $structure->attributes() as $attributeName => $attributeData )
            {
                $className          = $attributeData["class"];
                $attributeParams    = [];
                $attribute = new $className( $this->wc, $attributeName, $attributeParams, $this );
                $attribute->set($data[ $attributeName ]);
                
                $this->attributes[ $attributeName ] = $attribute;
            }
            
            $this->exist = true;
        }
        
        $this->structure    = $structure;
    }
    
    public function __set(string $name, mixed $value): void {
        $this->properties[$name] = $value;
    }
    
    public function __get(string $name): mixed {
        return $this->properties[$name];
    }    
    
    static function factory( WitchCase $wc, Structure $structure, array $data=null )
    {
        $className  = __CLASS__."\\".ucfirst($structure->type);
        
        return new $className( $wc, $structure, $data );
    }
    
    function attribute( string $attributeName ): ?Attribute {
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
        
    static function dbFields()
    {
        return array_unique(array_merge(
            self::DB_FIELDS,
            static::DB_FIELDS,
            [ self::PRIMARY_DB_FIELD ] 
        ));
    }
    
    function update( array $params )
    {
        if( $params['name'] ){
            $this->name = $params['name'];
        }
        
        foreach( $this->attributes as $attribute ){
            $attribute->update( $params );
        }
        
        $save = $this->save();
        
        if( $save ){
            $this->modified = null;
        }
        
        return $save;
    }
    
    function publish(){
        return $this->save();
    }
    
    function countWitches()
    {
        $table = $this->structure->table;
        
        return CraftDA::countWitches($this->wc, $table, $this->id);
    }
    
    function getRelatedCraftsIds( string $type  )
    {
        if( !in_array($type, self::TYPES) ){
            return false;
        }
        
        if( !isset($this->relatedCraftsIds[ $type ]) )
        {
            $table                              = $type.'__'.$this->structure->name;            
            if( property_exists($this, 'content_key') && $this->content_key ){
                $id = $this->content_key;
            }
            else {
                $id = $this->id;
            }
            $this->relatedCraftsIds[ $type ]   = CraftDA::getRelatedCraftsIds( $this->wc, $table, $id );
        }
        
        return $this->relatedCraftsIds[ $type ];
    }
    
    function getWitches( ?string $type=null )
    {
        if( $type && !in_array($type, self::TYPES) ){
            return false;
        }
        elseif( $type ){
            $table = $type.'__'.$this->structure->name;
        }
        else {
            $table = $this->structure->table;
        }
        
        if( isset($this->witches[ $table ]) ){
            return $this->witches[ $table ];
        }
        
        if( $type && $type !== static::TYPE 
            && property_exists($this, 'content_key') && $this->content_key
        ){
            $dataArray = CraftDA::getWitchesFromContentKey($this->wc, $table, $this->content_key) ?? [];
        }
        else {
            $dataArray = CraftDA::getWitches($this->wc, $table, $this->id) ?? [];
        }
        
        $this->witches[ $table ] = [];
        foreach( $dataArray as $data ){
            $this->witches[ $table ][ $data['id'] ] = Witch::createFromData($this->wc, $data);
        }
        
        return $this->witches[ $table ];
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
            
            if( CraftDA::delete($this->wc, $table, $this->id) ){
                $this->wc->cairn->remove( $table, $this->id );
            }
            
            if( property_exists($this, 'content_key') && $this->content_key ){
                CraftDA::cleanupContentKey( $this->wc, $this->structure->name, $this->content_key );
            }
        }
        catch( \Exception $e )
        {
            $this->wc->log->error($e->getMessage());
            $this->wc->db->rollback();
            return false;
        }
        $this->wc->db->commit();
        
        return true;
    }
    
    function save()
    {
        $this->wc->db->begin();
        try {
            if( property_exists($this, 'content_key') && !empty($this->content_key) ){
                $contentKey = $this->content_key;
            }
            else {
                $contentKey = null;
            }
            
            if( !$this->id ){
                $this->id   = $this->structure->createCraft( $this->name, $this->structure->type, $contentKey );
            }
            
            $updated = 0;
            foreach( $this->attributes as $attribute ){
                $updated += $attribute->save( $this );
            }
            
            $fields = [ 'name' => $this->name ];
            
            if( $contentKey ){
                $fields['content_key'] = $contentKey;
            }
            
            foreach( $this->attributes as $attribute ){
                foreach( $attribute->tableColumns as $key => $tableColumn  ){
                    $fields[ $tableColumn ] = $attribute->values[ $key ];
                }
            }
            
            $conditions = [ 'id' => $this->id ];
            
            $updated += CraftDA::update( $this->wc, $this->structure->table, $fields, $conditions );
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
    
    function createDraft()
    {
        $draftStructure = new Structure( $this->wc, $this->structure->name, Draft::TYPE );
        $draft          = Craft::factory( $this->wc, $draftStructure );
        
        $draft->name          = $this->name;
        
        if( property_exists($this, 'content_key') && $this->content_key ){
            $draft->content_key   = $this->content_key;
        }
        else {
            $draft->content_key   = $this->id;
        }        
        
        foreach( $this->attributes as $attributeName => $attribute ){
            $draft->attributes[ $attributeName ] = $attribute->clone( $draft );
        }
        
        $draft->save();
        
        return $draft;
    }
    
    function getDraft()
    {
        if( empty($this->getRelatedCraftsIds(Draft::TYPE)) ){
            return $this->createDraft();
        }
        
        $draftStructure = new Structure( $this->wc, $this->structure->name, Draft::TYPE );
        $craftData      = WitchCrafting::getCraftDataFromIds($this->wc, $draftStructure->table, $this->getRelatedCraftsIds(Draft::TYPE) );
        
        return Craft::factory( $this->wc, $draftStructure, array_values($craftData)[0] );
    }
    
    function type(): string {
        return static::TYPE;
    }
    
    function structure(): string {
        return $this->structure->name;
    }
}
