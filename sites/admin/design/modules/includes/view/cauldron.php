<?php /** @var WC\Module $this */ ?>

<div class="box view__cauldron">
    <?php if( !isset($cauldron) ): ?>
        <h3>
            <i class="fa fa-feather-alt"></i>
            No cauldron
        </h3>
        <form method="post" 
              action="<?=$this->wc->website->getUrl('edit?id='.$this->witch("target")->id) ?>"
              id="witch-add-new-content">
            <select name="witch-content-structure" id="witch-content-structure">
                <option value="">
                    Select new cauldron structure
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
            <?=$cauldron->name ?>
        </h3>
        <h4>
            <?=$cauldron->data->structure ?>
            <em>[<?=$cauldron->status ?> <?=$cauldron->id ?>]</em>
        </h4>
        
        <p><em>Cauldron (data) associated with this Witch</em></p>
        
        <?php  //$this->wc->debug($cauldron->content(), "ingredients", 2); ?>

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
                    data-action="remove-craft"
                    data-target="view-craft-action">
                <?php //if( count($craftWitches) == 1 ): ?>
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
                    data-href="<?=$this->wc->website->getUrl("cauldron?id=".$this->witch("target")->id) ?>"
                    id="cauldron__edit">
                <i class="fa fa-pencil"></i>
                Edit
            </button>
        </div>
    <?php endif; ?>
</div>

<form method="post" 
      action="<?=$this->wc->website->getUrl('edit?id='.$this->witch("target")->id) ?>"
      id="view-craft-action"></form>
