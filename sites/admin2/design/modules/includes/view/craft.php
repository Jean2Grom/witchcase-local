<?php 
    $this->addJsFile('triggers.js');
?>
<style>
    .view__craft fieldset {
        border-radius: 5px;
        margin-bottom: 10px;
    }
</style>
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
        <h3>
            <?=ucfirst($targetWitch->craft()->structure->type) ?>
            <em>
                [<?=$targetWitch->craft()->structure->name ?> <?=$targetWitch->craft()->id ?>]                
            </em>
        </h3>
        
        <h4><?=$targetWitch->craft()->name ?></h4>
        <p>
            <?php if( $targetWitch->craft()->created ): ?>
                <em>Created by <?=$targetWitch->craft()->created->actor?>: <?=$targetWitch->craft()->created->format( \DateTimeInterface::RFC2822 )?></em>
            <?php endif; ?>
            <?php if( $targetWitch->craft()->modified && $targetWitch->craft()->created != $targetWitch->craft()->modified ): ?>
                <br/> 
                <em>Modified by <?=$targetWitch->craft()->modified->actor?>: <?=$targetWitch->craft()->modified->format( \DateTimeInterface::RFC2822 )?></em>
            <?php endif; ?>
        </p>
        
        <?php foreach( $targetWitch->craft()->attributes as $attribute ): ?>
            <fieldset>
                <legend><?=$attribute->name?> [<?=$attribute->type?>]</legend>
                    <?php $attribute->display() ?>
            </fieldset>
            <div class="clear"></div>
        <?php endforeach; ?>

        <div class="box__actions">
            <button class="trigger-action"
                    data-confirm="Warning ! You are about to remove this content"
                    data-action="delete-content"
                    data-target="view-action">Delete</button>
            <?php if( $targetWitch->craft()->structure->type === WC\Craft\Content::TYPE ): ?>
                <button class="trigger-action"
                        data-confirm="Are you sure to archive this content ?"
                        data-action="archive-content"
                        data-target="view-craft-action">Archive</button>
            <?php endif; ?>
            <button class="trigger-href" 
                    data-href="<?=$editCraftContentHref ?>"
                    id="content__edit">Edit</button>
            <!--button class="trigger-action"
                    data-action="edit-content"
                    data-target="view-action">
                Editer
            </button-->
        </div>
    <?php endif; ?>
</div>

<form method="post" id="view-craft-action"></form>


<script>
$(document).ready(function()
{
    $('#witch-content-structure').change(function(){
        $('#witch__add-content').prop( 'disabled', ($(this).val() === '') );
    });
});
</script>