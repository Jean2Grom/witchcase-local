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
    ];

    const HISTORY_FIELDS = [
        "creator",
        "created",
        "modificator",
        "modified",
    ];

    const STATUS_PUBLISHED      = null;
    const STATUS_DRAFT          = 0;
    const STATUS_ARCHIVED       = 1;

    const DRAFT_FOLDER_STRUCT   = "wc-drafts-folder";
    const ARCHIVE_FOLDER_STRUCT = "wc-archives-folder";

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

    /** @var self[] */
    public array $children    = [];
    
    /** @var Ingredient[] */
    public array $ingredients = [];

    /** @var (self|Ingredient)[] */
    public array $pendingRemoveContents = [];

    protected $content;

    public ?self $target        = null;
    public ?self $draft         = null;

    public string $editPrefix   = "s";

    private ?string $inputID    = null;

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

    function isContent(): bool
    {
        if( in_array($this->data?->structure, [ self::DRAFT_FOLDER_STRUCT, self::ARCHIVE_FOLDER_STRUCT ]) ){
            return false;
        }

        return true;
    }

    /**
     * @return array|Ingredient|Cauldron|null
     */
    function content( ?string $name=null ): array|Ingredient|Cauldron|null
    {
        if( is_null($name) ){
            return $this->contents();
        }

        if( is_null($this->content) ){
            $this->generatContent();
        }

        foreach( $this->content as $content ){
            if( $content->name === $name ){
                return $content;
            }
        }

        return null;
    }

    /**
     * Display priority ordered array of content Ingredients and children Cauldron
     * @return Ingredient|Cauldron[]
     */
    function contents(): array
    {
        if( is_null($this->content) ){
            $this->generatContent();
        }

        return $this->content;
    }

    private function generatContent(): void
    {
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
            if( !$child->isContent() ){
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

        return;
    }

    function save( bool $transactionMode=true ): bool
    {
        if( !$transactionMode ){
            return $this->saveAction();
        }
        $this->wc->db->begin();

        try {
            if( !$this->saveAction() )
            {
                $this->wc->db->rollback();
                return false;
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

    private function saveAction(): bool
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
$this->wc->debug( $properties );

            Handler::writeProperties($this);
$this->wc->debug( $this->properties );            
$this->wc->debug( array_diff_assoc($this->properties, $properties) );            
            $result = DataAccess::update( $this, array_diff_assoc($this->properties, $properties) );
        }
        
        if( $result === false ){
            return false;
        }
        $result = true;
        
        // Deletion of pending deprecated contents
        $result = $result && $this->purge();

        // Saving contents
        foreach( $this->ingredients as $ingredient ){
            $result = $result && $ingredient->save();
        }

        foreach( $this->children as $child ) 
        {
            if( !$child->isContent() ){
                continue;
            }

            $result = $result && $child->save( false );
        }

        $this->inputID = null;

        return $result;
    }

    function purge(): bool 
    {
        $result = true;

        foreach( $this->pendingRemoveContents as $removingContent ){
            if( $removingContent->isIngredient() ){
                $result = $result && $removingContent->delete();
            }
            else {
                $result = $result && $removingContent->delete( false );
            }
        }

        return $result;
    }

    function delete( bool $transactionMode=true ): bool
    {
        if( $this->target->draft ){
            $this->target->draft = null;
        }

        if( !$transactionMode ){
            return $this->deleteAction();
        }
        $this->wc->db->begin();

        try {
            if( !$this->deleteAction() )
            {
                $this->wc->db->rollback();
                return false;
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

    private function deleteAction(): bool
    {
        // Deletion of pending deprecated contents
        $result = $this->purge();

        foreach( $this->ingredients as $ingredient ){
            $result = $result && $ingredient->delete();
        }

        foreach( $this->children as $child ) 
        {
            if( !is_a($child, __CLASS__) ){
                continue;
            }

            $result = $result && $child->delete( false );
        }

        if( $result === false ){
            return false;
        }
        
        if( $this->exist() ){
            return DataAccess::delete( $this ) !== false;
        }
        
        return true;
    }
  
    function add( Cauldron|Ingredient $content ): bool
    {
        // Cauldron case
        if( is_a($content, __CLASS__) ){
            return $this->addCauldron( $content );
        }

        // Ingredient case
        return $this->addIngredient( $content );
    }

    function addCauldron( Cauldron $cauldron ): bool
    {
        if( $this->depth == $this->wc->cauldronDepth ){
            DataAccess::increaseDepth( $this->wc );
        }

        $cauldron->position = [];
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

    function readInputs( ?array $inputs=null, bool $contentAutoPriority=true ): self
    {
        $params = $inputs ?? $this->wc->request->inputs();
        
        if( isset($params['name']) ){
            $this->name = htmlspecialchars($params['name']);
        }

        if( isset($params['priority']) && is_int($params['priority']) ){
            $this->priority = $params['priority'];
        }

        if( !$params['content'] ){
            $params['content'] = [];
        }

        if( $contentAutoPriority )
        {
            $priorityInterval   = 100;
            $priority           = count($params['content']) * $priorityInterval;
        }

        $contents           = $this->contents();
        $this->content      = null;
        $this->ingredients  = [];
        $this->children     = [];
        foreach( $params['content'] as $indice => $contentParams )
        {
            if( $contentAutoPriority )
            {
                $contentParams[ 'priority' ]    =   $priority;
                $priority                       -=  $priorityInterval;
            }

            if( isset($contents[ $indice ]) 
                && $contents[ $indice ]->type === $contentParams['type'] 
                && (    !$contents[ $indice ]->exist() 
                        || $contents[ $indice ]->id === (int) ($contentParams['ID'] ?? 0)   
                )
            ){
                if( in_array($contents[ $indice ]->type ?? "", Ingredient::list()) ){                    
                    $this->ingredients[] = $contents[ $indice ]->readInputs( $contentParams );
                }
                else {
                    $this->children[] = $contents[ $indice ]->readInputs( $contentParams, $contentAutoPriority );
                }

                unset($contents[ $indice ]);
            }
            elseif( in_array($contentParams['type'] ?? "", Ingredient::list()) ){
                IngredientHandler::createFromData( 
                    $this, 
                    $contentParams['type'], 
                    [ 
                        'name'      => $contentParams['name'], 
                        'value'     => $contentParams['value'] ?? null, 
                        'priority'  => $contentParams['priority'], 
                    ] 
                );
            }
            else 
            {
                $newChild = Handler::createFromData($this->wc, [
                    'name'      =>  $contentParams['name'],
                    'data'      => json_encode([ 'structure' => $contentParams['type'] ?? "folder" ]),
                    'priority'  => $contentParams['priority'],
                ]);

                $this->addCauldron( $newChild->readInputs($contentParams, $contentAutoPriority) );
            }
        }

        foreach( $contents as $unmatchedContent ){
            $this->pendingRemoveContents[] = $unmatchedContent;
        }

        return $this;
    }

    function draft(): self
    {
        if( $this->isDraft() ){
            return $this;
        }
        elseif( $this->draft ){
            return $this->draft;
        }

        $folder = Handler::getDraftFolder( $this );

        foreach( $folder->children as $child ){
            if( $child->isDraft() && $child->targetID === $this->id )
            {
                $child->target  = $this;
                return $child;
            }
        }

        $this->draft  = Handler::createDraft( $this );
        $folder->addCauldron( $this->draft );
        
        return $this->draft;
    }

    function getInputIdentifier(): string 
    {
        if( $this->inputID ){
            return $this->inputID;
        }

        $this->inputID = str_replace( ' ', '-', $this->name ?? "" ).'#';

        if( $this->parent ){
            $this->inputID .= array_keys(array_intersect(
                $this->parent->children, 
                [$this]
            ))[0] ?? "";
        }

        return $this->inputID;
    }

    function publish( bool $transactionMode=true ): bool
    {
        if( !$transactionMode ){
            return $this->publishAction();
        }
        $this->wc->db->begin();

        try {
            if( !$this->publishAction() )
            {
                $this->wc->db->rollback();
                return false;
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

    function publishAction(): bool
    {
        $target = false;
        if( $this->target ){
            $target = $this->target;
        }
        elseif( $this->targetID ){
            $target = Handler::fetch( $this->wc, ['id' => $this->targetID] );
        }

        if( $target )
        {
            // Create archive 
            $archiveFolder  = Handler::getArchiveFolder($target);

            Handler::writeProperties($target);
            $archiveProperties              = $target->properties;
            $archiveProperties['target']    = $target->id;
            $archiveProperties['status']    = Cauldron::STATUS_ARCHIVED;
            unset( $archiveProperties['id'] );

            $archive            = Handler::createFromData( $this->wc, $archiveProperties );
            $archive->target    = $target;

            $archiveFolder->addCauldron( $archive );

            // Move published target content to created archive
            foreach( $target->ingredients as $content ){
                $archive->add( $content );
            }
            $target->ingredients = [];

            foreach( $target->children as $key => $child ){
                if( $child->isContent() )
                {
                    $archive->add( $child );
                    unset( $target->children[$key] );
                }
            }

            // Update published target
            $target->name = $this->name;
            $target->data = $this->data;

            // Move this (draft) content to published target
            foreach( $this->ingredients as $content ){
                $target->add( $content );
            }
            $this->ingredients = [];

            foreach( $this->children as $key => $child ){
                if( $child->isContent() )
                {
                    $target->add( $child );
                    unset( $this->children[$key] );
                }
            }

            $target->save( false );
            $archive->save( false );
            $this->delete( false );
        }
        else 
        {
            $this->status = null;
            $this->save( false );
            if( $this->targetID ){
                DataAccess::updateTargetID( $this->wc, $this->targetID, $this->id );
            }
        }

        return true;
    }
}