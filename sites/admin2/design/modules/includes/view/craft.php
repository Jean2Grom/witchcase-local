<div class="box view__craft">
    <?php if( empty($targetWitch->craft()) ): ?>
        <h3>No craft</h3>
        <form method="post" id="witch-add-new-content">
            <select name="witch-content-structure" id="witch-content-structure">
                <option value="">
                    Choose structure
                </option>
                <?php foreach( $structuresList as $structureData ): ?>
                    <option value="<?=$structureData['name']?>">
                        <?=$structureData['name']?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        
        <div class="box__actions">
            <button id="witch__add-content" disabled
                    class="trigger-action"
                    data-action="add-content"
                    data-target="witch-add-new-content">Create craft</button>
        </div>

    <?php else: ?>
        <h3><?=$targetWitch->craft()->name ?></h3>        
        <h4>
            <?=ucfirst($targetWitch->craft()->structure->name) ?>
            <em>[<?=$targetWitch->craft()->structure->type ?> <?=$targetWitch->craft()->id ?>]</em>
        </h4>
        
        <p><em>Craft (content) associated with this witch, can have more than one association</em></p>
        
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
                    data-action="delete-content"
                    data-target="view-craft-action">Delete</button>
            <?php if( $targetWitch->craft()->structure->type === WC\Craft\Content::TYPE ): ?>
                <button class="trigger-action"
                        data-confirm="Are you sure to archive this content ?"
                        data-action="archive-content"
                        data-target="view-craft-action">Archive</button>
            <?php endif; ?>
            <button class="trigger-href" 
                    data-href="<?=$this->wc->website->getUrl("edit-content?id=".$targetWitch->id) ?>"
                    id="content__edit">Edit</button>
        </div>
    <?php endif; ?>
</div>

<form method="post" id="view-craft-action"></form>
