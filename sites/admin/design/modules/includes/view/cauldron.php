<?php 
/** 
 * @var WC\Module $this 
 * @var WC\Cauldron $cauldron
 */
?>

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
            <button id="witch-create-cauldron" disabled
                    class="trigger-action"
                    data-action="create-cauldron"
                    data-target="witch-add-new-cauldron">
                <i class="fa fa-plus"></i>
                Create new cauldron
            </button>
        </div>

    <?php else: ?>
        <h3>
            <i class="fa fa-feather-alt"></i>
            <?=$cauldron->name ?>
        </h3>
        <h4 title="ID <?=$cauldron->id ?>">
            <?=$cauldron->data->structure ?>
            <em>[<?=$cauldron->isPublished()? 
                        "Published": 
                        ($cauldron->isDraft()? 
                            "Draft": 
                            "Archive")?>]</em>
        </h4>
        
        <p><em>Cauldron (data) associated with this Witch</em></p>
        
        <?php  $this->wc->debug($cauldron->content(), "ingredients", 2); ?>

        <?php foreach( $cauldron->content() as $ingredient ): ?>
            <fieldset>
                <legend><?=$ingredient->name?> [<?=$ingredient->type?>]</legend>                    
                <?php $ingredient->display() ?>
            </fieldset>
        <?php endforeach; ?>
        
        <p>
            <?php /*if( $this->witch("target")->craft()->created ): ?>
                <em>Created by <?=$this->witch("target")->craft()->created->actor?>: <?=$this->witch("target")->craft()->created->format( \DateTimeInterface::RFC2822 )?></em>
            <?php endif; ?>
            <?php if( $this->witch("target")->craft()->modified && $this->witch("target")->craft()->created != $this->witch("target")->craft()->modified ): ?>
                <br/> 
                <em>Modified by <?=$this->witch("target")->craft()->modified->actor?>: <?=$this->witch("target")->craft()->modified->format( \DateTimeInterface::RFC2822 )?></em>
            <?php endif;*/ ?>
        </p>
        
        <div class="box__actions">
            <button class="trigger-action"
                    data-confirm="Warning ! You are about to remove this content"
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
            <?php /*if( $this->witch("target")->craft()->structure->type === WC\Craft\Content::TYPE ): ?>
                <button class="trigger-action"
                        data-confirm="Are you sure to archive this content ?"
                        data-action="archive-craft"
                        data-target="view-craft-action">
                    <i class="fa fa-archive"></i>
                    Archive
                </button>
            <?php endif; */?>
            <button class="trigger-href" 
                    data-href="<?=$this->wc->website->getUrl("cauldron?id=".$this->witch("target")->id) ?>">
                <i class="fa fa-pencil"></i>
                Edit
            </button>
        </div>
    <?php endif; ?>
</div>

<form method="post" id="view-cauldron-action"></form>
