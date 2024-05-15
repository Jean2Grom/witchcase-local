<?php 
namespace WC;

use WC\DataAccess\CauldronDataAccess as DataAccess;
use WC\Handler\CauldronHandler as Handler;
use WC\Handler\IngredientHandler;
use WC\Trait\CauldronIngredientTrait;

class Cauldron
{
    use CauldronIngredientTrait;

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

    public ?self $target        = null;

    public string $editPrefix   = "s";

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
            return str_replace(' ', '-', $this->data->structure) ?? "cauldron";
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


    function save(): bool
    {
        if( $this->depth > $this->wc->cauldronDepth ){
            DataAccess::increaseDepth( $this->wc );
        }
        
        if( !$this->exist() )
        {
            Handler::writeProperties($this);
            $result = DataAccess::insert($this);            
            if( $result ){
                $this->id = (int) $result;
            }
        }
        else 
        {
            $properties = $this->properties;
            Handler::writeProperties($this);
            $result = DataAccess::update($this, array_diff_assoc($properties, $this->properties));
        }
        
        if( $result === false ){
            return false;
        }
        
        $result = true;
        foreach( $this->content() as $content ){
            $result = $result && $content->save();
        }

        return $result;
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
        $cauldron->depth                        = $this->depth + 1;

        Handler::setParenthood($this, $cauldron);
        $this->content = null;

        foreach( $cauldron->children as $child ){
            $cauldron->addCauldron( $child );
        }

        return true;
    }

    function addIngredient( Ingredient $ingredient ): bool
    {
        Handler::setIngredient($this, $ingredient);
        $this->content = null;

        return true;
    }

    function readInputs( ?array $inputs=null ): self
    {
        $params = $inputs ?? $this->wc->request->inputs();

        if( isset($params['name']) ){
            $this->name = htmlspecialchars($params['name']);
        }

        $matchedIngredients = [];
        $newIngredients     = [];
        $doneChildPrefix    = [];
        $matchedChildren    = [];
        $newChildren        = [];
        foreach( $params as $param => $value )
        {
            if( substr($param, 0, 2) === 'i#' )
            {
                $buffer = explode('#', substr($param, 2) );

                $type   = $buffer[0];
                $name   = $buffer[1];
                $index  = $buffer[2];

                $match  = false;
                foreach( $this->ingredients as $ingredient ){
                    if( $ingredient->type === $type 
                        && $ingredient->getInputIdentifier() === $name.'#'.$index
                        && !in_array($ingredient, $matchedIngredients)
                    ){
                        $matchedIngredients[]   = $ingredient->readInput( $value );
                        $match                  = true;
                        break;
                    }
                }

                if( !$match ){
                    $newIngredients[] = IngredientHandler::createFromData( 
                        $this, 
                        $type, 
                        [ 
                            'name'  => $name,  
                            'value' => $value,  
                        ]);
                }
            }
            elseif( substr($param, 0, 2) === 's#' )
            {
                $prefix = strstr( $param, '|', true );

                if( in_array($prefix, $doneChildPrefix) ){
                    continue;
                }

                $doneChildPrefix[] = $prefix;
                
                $buffer = explode('#', substr($prefix, 2) );

                $type   = $buffer[0];
                $name   = $buffer[1];
                $index  = $buffer[2];

                $innerInputs = [];
                foreach( $params as $key => $val ){
                    if( str_starts_with($key, $prefix.'|') ){
                        $innerInputs[ substr( $key, strlen($prefix)+1 ) ] = $val;
                    } 
                }
                
                $match  = false;
                foreach( $this->children as $child ){
                    if( $child->type === $type 
                        && $child->getInputIdentifier() === $name.'#'.$index
                        && !in_array($child, $matchedChildren)
                    ){
                        $matchedChildren[]  = $child->readInputs( $innerInputs );
                        $match              = true;
                        break;
                    }
                }

                if( !$match )
                {
                    $newChild = Handler::createFromData($this->wc, [
                        'name'  => $name,
                        'data'  => json_encode([ 'structure' => $type ]),
                    ]);
                    $newChildren[] = $newChild->readInputs( $innerInputs );
                    $this->addCauldron($newChild);
                }
            }
        }

        $removedIngredientsKeys = array_keys(array_diff(
            $this->ingredients, 
            array_merge($matchedIngredients, $newIngredients)
        ));

        foreach( $removedIngredientsKeys as $key ){
            unset($this->ingredients[ $key ]);
        }

        $removedChildrenKeys = array_keys(array_diff(
            $this->children, 
            array_merge($matchedChildren, $newChildren)
        ));

        foreach( $removedChildrenKeys as $key ){
            unset($this->children[ $key ]);
        }

        return $this;
    }

    function draft()
    {
        if( $this->isDraft() ){
            return $this;
        }

        $folder = Handler::getDraftFolder( $this );

        foreach( $folder->children as $child ){
            if( $child->isDraft() && $child->targetID === $this->id )
            {
                $child->target  = $this;
                return $child;
            }
        }

        $draft  = Handler::createDraft( $this );
        $folder->addCauldron($draft);
        
        return $draft;
    }

    function getInputIdentifier(): string 
    {
        $prefix = str_replace( ' ', '-', $this->name).'#';

        if( $this->parent ){
            return $prefix.array_keys(array_intersect(
                $this->parent->children, 
                [$this]
            ))[0] ?? "";
        }

        return $prefix;
    }

}