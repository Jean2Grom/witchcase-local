<?php 
namespace WC;

use WC\Trait\DisplayTrait;

class Cauldron
{
    use DisplayTrait;

    const FIELDS = [
        "id",
        "content_key",
        "status",
        "name",
        "resume",
        "data",
        "priority",
        "datetime",
    ];

    const DIR                   = "cauldron/structures";
    const DESIGN_SUBFOLDER      = "design/cauldron/structures";

    public $properties  = [];

    public $id;
    public $status      = 'content';
    public $contentCauldronID;
    public $contentCauldron;
    public $name;
    public $resume;
    public $data;
    public $priority;
    public $datetime;
    
    public $depth       = 0;
    public $position    = [];

    public $parent;
    public $children    = [];
    public $ingredients = [];

    protected $content;

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
        $this->properties[ $name ] = $value;
    }
    
    /**
     * Property reading
     * @param string $name
     * @return mixed
     */
    public function __get( string $name ): mixed 
    {
        if( $name === 'type' ){
            return $this->data->structure ?? "cauldron";
        }
        return $this->properties[ $name ] ?? null;
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
    

    function isParentOf( self $potentialChild ): bool
    {
        if( $potentialChild->depth != $this->depth + 1 ){
            return false;
        }
        
        $isParent = true;
        for( $i=1; $i<=$this->depth; $i++ ){
            if( $this->position[ $i ] != $potentialChild->position[ $i ] )
            {
                $isParent = false;
                break;
            }
        }

        return $isParent;
    }

    function content(): array
    {
        if( !is_null($this->content) ){
            return $this->content;
        }

        $buffer     = [];
        $defaultId  = 0;

        foreach( $this->ingredients as $ingredientsTypeArray  ){
            foreach( $ingredientsTypeArray as $ingredient )
            {
                $priority   = $ingredient->priority ?? 0;
                $key        = ($ingredient->name ?? "")."_".($ingredient->id ?? $defaultId++);
                $buffer[ $priority ] = array_replace( 
                    $buffer[ $priority ] ?? [], 
                    [ $key => $ingredient ]
                );
            }
        }
        
        foreach( $this->children as $child )
        {
            $priority   = $child->priority ?? 0;
            $key        = ($child->name ?? "")."_".($child->id ?? $defaultId++);
            $buffer[ $priority ] = array_replace( 
                $buffer[ $priority ] ?? [], 
                [ $key => $child ]
            );
        }

        $this->content = [];

        krsort($buffer);
        foreach( $buffer as $priorityArray )
        {
            ksort($priorityArray);
            foreach( $priorityArray as $contentItem ){
                $this->content[] = $contentItem;
            }
        }

        return $this->content;
    }

}