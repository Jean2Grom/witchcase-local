<?php /** @var WC\Module $this */ ?>

<div class="box view__craft">
    <?php if( empty($targetWitch->craft()) ): ?>
        <h3>
            <i class="fa fa-feather-alt"></i>
            No craft
        </h3>
        <form method="post" 
              action="<?=$this->wc->website->getUrl('edit?id='.$targetWitch->id) ?>"
              id="witch-add-new-content">
            <select name="witch-content-structure" id="witch-content-structure">
                <option value="">
                    Select new craft structure
                </option>
                <?php foreach( $structuresList as $structureData ): ?>
                    <option value="<?=$structureData['name']?>">
                        <?=$structureData['name']?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="hidden" id="imported-craft-witch" name="imported-craft-witch" value="" />
        </form>
        
        <div class="box__actions">
            <button id="import-craft-action" 
                    class="trigger-action"
                    style="display: none;"
                    data-action="import-craft"
                    data-target="witch-add-new-content">Import craft</button>
            <button id="witch-get-existing-craft">
                <i class="fa fa-project-diagram"></i>
                Get existing craft
            </button>
            <button id="witch-create-craft" disabled
                    class="trigger-action"
                    data-action="create-craft"
                    data-target="witch-add-new-content">
                <i class="fa fa-plus"></i>
                Create craft
            </button>
        </div>

    <?php else: ?>
        <h3>
            <i class="fa fa-feather-alt"></i>
            <?=$targetWitch->craft()->name ?>
        </h3>        
        <h4>
            <?=ucfirst($targetWitch->craft()->structure->name) ?>
            <em>[<?=$targetWitch->craft()->structure->type ?> <?=$targetWitch->craft()->id ?>]</em>
        </h4>
        
        <p><em>Craft (content) associated with this witch</em></p>
        
        <?php foreach( $targetWitch->craft()->attributes as $attribute ): ?>
            <fieldset>
                <legend><?=$attribute->name?> [<?=$attribute->type?>]</legend>
                    <?php $attribute->display() ?>
            </fieldset>
            <div class="clear"></div>
        <?php endforeach; ?>
        
        <p>
            <?php if( $targetWitch->craft()->created ): ?>
                <em>Created by <?=$targetWitch->craft()->created->actor?>: <?=$targetWitch->craft()->created->format( \DateTimeInterface::RFC2822 )?></em>
            <?php endif; ?>
            <?php if( $targetWitch->craft()->modified && $targetWitch->craft()->created != $targetWitch->craft()->modified ): ?>
                <br/> 
                <em>Modified by <?=$targetWitch->craft()->modified->actor?>: <?=$targetWitch->craft()->modified->format( \DateTimeInterface::RFC2822 )?></em>
            <?php endif; ?>
        </p>
        
        <div class="box__actions">
            <button class="trigger-action"
                    data-confirm="Warning ! You are about to remove this content"
                    data-action="remove-craft"
                    data-target="view-craft-action">
                <?php if( count($craftWitches) == 1 ): ?>
                    <i class="fa fa-trash"></i>
                    Delete
                <?php else: ?>
                    <i class="fa fa-times"></i>
                    Remove
                <?php endif;?>
            </button>
            <?php if( $targetWitch->craft()->structure->type === WC\Craft\Content::TYPE ): ?>
                <button class="trigger-action"
                        data-confirm="Are you sure to archive this content ?"
                        data-action="archive-craft"
                        data-target="view-craft-action">
                    <i class="fa fa-archive"></i>
                    Archive
                </button>
            <?php endif; ?>
            <button class="trigger-href" 
                    data-href="<?=$this->wc->website->getUrl("edit-content?id=".$targetWitch->id) ?>"
                    id="content__edit">
                <i class="fa fa-pencil"></i>
                Edit
            </button>
        </div>
    <?php endif; ?>
</div>

<form method="post" 
      action="<?=$this->wc->website->getUrl('edit?id='.$targetWitch->id) ?>"
      id="view-craft-action"></form>
