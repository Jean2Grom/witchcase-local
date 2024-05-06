<?php 
namespace WC;

use WC\DataAccess\CauldronDataAccess as DataAccess;
use WC\Handler\CauldronHandler as Handler;
use WC\Trait\CauldronContentTrait;

class Cauldron
{
    use CauldronContentTrait;

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

    const DRAFT_FOLDER_NAME     = "wc-drafts-folder";

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

    public ?self $target    = null;

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

        foreach( $this->ingredients as $ingredient )
        {
            $priority   = $ingredient->priority ?? 0;
            $key        = ($ingredient->name ?? "")."_".($ingredient->id ?? $defaultId++);
            $buffer[ $priority ] = array_replace( 
                $buffer[ $priority ] ?? [], 
                [ $key => $ingredient ]
            );
        }
        
        foreach( $this->children as $child )
        {
            if( $child->name === Cauldron::DRAFT_FOLDER_NAME 
                && $child->data->structure === "folder" ){
                continue;
            }

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


    function save(): self
    {
        if( $this->depth > $this->wc->cauldronDepth ){
            DataAccess::increaseDepth( $this->wc );
        }

        if( !$this->exist() )
        {
            Handler::writeProperties($this);
            DataAccess::insert($this);
        }
        else 
        {
            $properties = $this->properties;
            Handler::writeProperties($this);
            DataAccess::update($this, array_diff_assoc($properties, $this->properties));
        }

        foreach( $this->content() as $content ){
            $content->save();
        }

        return $this;
    }

    function add( Cauldron|Ingredient $content ): bool
    {
        // Ingredient case
        if( get_class($content) !== __CLASS__ ){
            return $this->addIngredient( $content );
        }

        // Cauldron case
        $this->wc->db->begin();
        try {
            $this->addCauldron( $content );
            $this->save();
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

    function addCauldron( Cauldron $cauldron ): bool
    {
        if( $this->depth == $this->wc->cauldronDepth ){
            DataAccess::increaseDepth( $this->wc );
        }

        foreach( $this->position as $level => $value ){
            $cauldron->position[ $level ] = $value;
        }
        $cauldron->position[ $this->depth + 1 ] = DataAccess::getNewPosition( $this );

        Handler::setParenthood($this, $cauldron);
        $this->content = null;

        foreach( $cauldron->content() as $subcontent ){
            // Ingredient case
            if( get_class($subcontent) !== __CLASS__ ){
                $this->addIngredient( $subcontent );
            }
            else {
                $cauldron->addCauldron( $subcontent );
            }            
        }

        return true;
    }

    function addIngredient( Ingredient $ingredient ): bool
    {
        Handler::setIngredient($this, $ingredient);
        $this->content = null;

        return true;
    }

    function draft()
    {
        if( $this->isDraft() ){
            return $this;
        }

        $folder = Handler::getDraftFolder( $this );

        // TODO : look for draft
        $draft = false;
        foreach( $folder->content() as $content ){
            if( true ){
                //$draft = $content;
            }
        }

        if( !$draft )
        {
            $draft  = Handler::createDraft( $this );
            //$folder->add($draft);
            
            //$this->wc->debug($draft);
            /*
            $this->wc->debug($draft->ingredients);
            $this->wc->debug($draft->children, "draft", 2);
            $this->wc->debug($this->children, "this", 2);
            $this->wc->debug($this->content(), "this", 2);
            */
        }

        //$this->wc->dump($draft);

        return $draft;
    }
}