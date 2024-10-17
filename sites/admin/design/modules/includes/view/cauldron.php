<?php /** @var WC\Module $this */ ?>

<div class="box view__cauldron">
    <?php if( !$this->witch("target")->hasCauldron() ): ?>
        <h3>
            <i class="fa fa-feather-alt"></i>
            No cauldron
        </h3>
        <form method="post" id="witch-add-new-cauldron">

            <select name="witch-cauldron-structure" id="witch-cauldron-structure">
                <option value="">
                    Select new cauldron structure
                </option>
                <?php foreach( $this->wc->configuration->structures() as $structure ): ?>
                    <option value="<?=$structure->name?>">
                        <?=$structure->name?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="hidden" id="imported-cauldron-witch" name="imported-cauldron-witch" value="" />
        </form>
        
        <div class="box__actions">
            <button id="import-cauldron-action" 
                    class="trigger-action"
                    style="display: none;"
                    data-action="import-cauldron"
                    data-target="witch-add-new-cauldron">Import craft</button>
            <button id="witch-get-existing-cauldron">
                <i class="fa fa-project-diagram"></i>
                Get existing cauldron
            </button>
            <button id="witch-create-cauldron" 
                    class="trigger-action disabled"
                    data-action="create-cauldron"
                    data-target="witch-add-new-cauldron">
                <i class="fa fa-plus"></i>
                Create new cauldron
            </button>
        </div>

    <?php else: ?>
        <h3>
            <i class="fa fa-feather-alt"></i>
            <?=$this->witch("target")->cauldron()->name ?>
        </h3>
        <h4 title="ID <?=$this->witch("target")->cauldron()->id ?>">
            <?=$this->witch("target")->cauldron()->type ?>
            <em>[<?=$this->witch("target")->cauldron()->isPublished()? 
                        "Published": 
                        ($this->witch("target")->cauldron()->isDraft()? "Draft": "Archive")?>]</em>
        </h4>

        <p><em>Cauldron is a content associated with the Witch</em></p>
                
        <?php foreach( $this->witch("target")->cauldron()->contents() as $ingredient ): ?>
            <fieldset>
                <legend><?=$ingredient->name?> [<?=$ingredient->type?>]</legend>                    
                <?php $ingredient->display() ?>
            </fieldset>
        <?php endforeach; ?>
        
        <p>
            <?php if( $this->witch("target")->cauldron()->created ): ?>
                <em>Created by <?=$this->witch("target")->cauldron()->created->actor?>: <?=$this->witch("target")->cauldron()->created->format( \DateTimeInterface::RFC2822 )?></em>
            <?php endif; ?>
            <?php if( $this->witch("target")->cauldron()->modified && $this->witch("target")->cauldron()->created != $this->witch("target")->cauldron()->modified ): ?>
                <br/> 
                <em>Modified by <?=$this->witch("target")->cauldron()->modified->actor?>: <?=$this->witch("target")->cauldron()->modified->format( \DateTimeInterface::RFC2822 )?></em>
            <?php endif; ?>
        </p>
        
        <div class="box__actions">
            <button class="trigger-action"
                    data-confirm="Confirm removal"
                    data-action="remove-cauldron"
                    data-target="view-cauldron-action">
                <?php //if( count($cauldronWitches) == 1 ): ?>
                <?php if( true ): ?>
                    <i class="fa fa-trash"></i>
                    Delete
                <?php else: ?>
                    <i class="fa fa-times"></i>
                    Remove
                <?php endif;?>
            </button>
            <?php if( !$this->witch("target")->cauldron()->isArchive() ): ?>
                <button class="trigger-action"
                        data-confirm="Confirm archive"
                        data-action="archive-cauldron"
                        data-target="view-cauldron-action">
                    <i class="fa fa-archive"></i>
                    Archive
                </button>
            <?php endif; ?>
            <button class="trigger-href" 
                    data-href="<?=$this->wc->website->getUrl("cauldron?id=".$this->witch("target")->id) ?>">
                <i class="fa fa-pencil"></i>
                Edit
            </button>
        </div>
    <?php endif; ?>
</div>

<form method="post" id="view-cauldron-action"></form>
