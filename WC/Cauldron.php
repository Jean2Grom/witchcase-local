<?php 
namespace WC;

class Cauldron
{
    public $properties  = [];

    public $id;
    public $status      = 'content';
    public $contentID;
    public $content;
    public $name;
    //public $resume;
    public $data;
    public $datetime;
    
    public $depth       = 0;
    public $position    = [];
    
    public $parent;
    public $siblings;
    public $children;
    
    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;
    

    /**
     * Property setting 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set( string $name, mixed $value ): void {
        $this->properties[$name] = $value;
    }
    
    /**
     * Property reading
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed {
        return $this->properties[$name] ?? null;
    }
    
    /**
     * Name reading
     * @return string
     */
    public function __toString(): string {
        return $this->name ?? "";
    }
    
    /**
     * Is this cauldron exist in database ?
     * @return bool
     */
    function exist(): bool {
        return !empty($this->id);
    }
    
    /**
     * Is this cauldron a content ?
     * @return bool
     */
    function isContent(): bool {
        return empty( $this->properties['status'] );
    }
    
    /**
     * Is this cauldron a draft ?
     * @return bool
     */
    function isDraft(): bool {
        return $this->properties['status'] === 0;
    }
    
    /**
     * Is this cauldron a archive ?
     * @return bool
     */
    function isArchive(): bool {
        return $this->properties['status'] === 1;
    }
    

    /*
    ["id"] => 9
    ["content_key"] => null
    ["status"] => null
    ["name"] => "profiles"
    ["resume"] => null
    ["data"] => "{"stucture": "array"}"
    ["priority"] => 0
    ["datetime"] => null
    ["level_1"] => 1
    ["level_2"] => 1
    ["level_3"] => 2
    ["level_4"] => 1
    ["level_5"] => 1
    ["bool"] => null
    ["bool_id"] => null
    ["bool_name"] => null
    ["bool_priority"] => null
    ["datetime_id"] => null
    ["datetime_name"] => null
    ["datetime_priority"] => null
    ["float"] => null
    ["float_id"] => null
    ["float_name"] => null
    ["float_priority"] => null
    ["int"] => null
    ["int_id"] => null
    ["int_name"] => null
    ["int_priority"] => null
    ["price"] => null
    ["price_id"] => null
    ["price_name"] => null
    ["price_priority"] => null
    ["string"] => null
    ["string_id"] => null
    ["string_name"] => null
    ["string_priority"] => null
    ["text"] => null
    ["text_id"] => null
    ["text_name"] => null
    ["text_priority"] => null
    ["identifier_table"] => "user__profile"
    ["identifier"] => 1
    ["identifier_id"] => 4
    ["identifier_name"] => null
    ["identifier_priority"] => 0
    ["link"] => null
    ["link_id"] => null
    ["link_name"] => null
    ["link_priority"] => null
    */
}