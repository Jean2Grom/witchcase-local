<?php 
namespace WC;

use WC\DataAccess\Cauldron as DataAccess;
use WC\Handler\CauldronHandler as Handler;
use WC\Trait\DisplayTrait;

class Cauldron
{
    use DisplayTrait;

    const FIELDS = [
        "id",
        "target",
        "status",
        "name",
        "data",
        "priority",
        "datetime",
    ];

    const STATUS_PUBLISHED      = null;
    const STATUS_DRAFT          = 0;
    const STATUS_ARCHIVED       = 1;

    const DIR                   = "cauldron/structures";
    const DESIGN_SUBFOLDER      = "design/cauldron/structures";

    public array $properties  = [];

    public ?int $id;
    public ?int $status;
    public ?int $targetID;
    public ?string $name;
    public ?\stdClass $data;
    public ?int $priority;
    public ?\DateTime $datetime;
    
    public int $depth       = 0;
    public array $position  = [];

    public ?self $parent;
    public array $children    = [];
    public array $ingredients = [];

    protected $content;

    public ?self $target;

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
        return $this->name ?? "Cauldron".(isset($this->id)? ": ".$this->id : "");
    }
    
    /**
     * Is this cauldron exist in database ?
     * @return bool
     */
    function exist(): bool {
        return !empty($this->id);
    }
    
    /**
     * Is this cauldron a published content ?
     * @return bool
     */
    function isPublished(): bool {
        return $this->status === self::STATUS_PUBLISHED;
    }
    
    /**
     * Is this cauldron a draft ?
     * @return bool
     */
    function isDraft(): bool {
        return $this->status === self::STATUS_DRAFT;
    }
    
    /**
     * Is this cauldron a archive ?
     * @return bool
     */
    function isArchive(): bool {
        return $this->status === self::STATUS_ARCHIVED;
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

    function save(): bool
    {
        if( $this->depth > $this->wc->cauldronDepth ){
            DataAccess::increaseDepth( $this->wc );
        }

        if( !$this->exist() )
        {            
            Handler::writeProperties($this);
            DataAccess::insert($this);
        }
        else {
            //DataAccess::update($this);
        }

        return true;
    }

    function add( Cauldron|Ingredient $content ): bool
    {
        // Ingredient case
        if( get_class($content) !== __CLASS__ )
        {
            // TODO writeProperties 
            $content->cauldron_fk = $this->id;
            // TODO save 
            $this->content = null;

            return true;
        }

        // Cauldron case
        if( $this->depth == $this->wc->cauldronDepth ){
            DataAccess::increaseDepth( $this->wc );
        }

        foreach( $this->position as $level => $value ){
            $content->position[ $level ] = $value;
        }
        $content->position[ $this->depth + 1 ] = DataAccess::getNewPosition( $this );

        //Handler::writeProperties( $content );
        $content->save();
        // TODO mode transactionnel
        foreach( $content->content() as $subcontent ){
            $content->add( $subcontent );
        }

        return true;
    }


    function draft()
    {
        //$this->wc->debug($this, "" , 2);
        //$this->wc->debug( in_array(null, [ 1, null, 23]) );
        
        if( $this->isDraft() ){
            return $this;
        }

        // TODO : look for draft


        return Handler::createDraft( $this );

        //return $this;
        /*
        if( empty($this->getRelatedCraftsIds(Draft::TYPE)) ){
            return $this->createDraft();
        }
        
        $draftStructure = new Structure( $this->wc, $this->structure->name, Draft::TYPE );
        $craftData      = WitchCrafting::getCraftDataFromIds($this->wc, $draftStructure->table, $this->getRelatedCraftsIds(Draft::TYPE) );
        
        return Craft::factory( $this->wc, $draftStructure, array_values($craftData)[0] );
        */
    }
}