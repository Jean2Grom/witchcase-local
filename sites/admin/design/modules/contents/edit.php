<?php
    $this->addCssFile('content-edit.css');
    $this->addJsFile('triggers.js');
?>
<h1><?=$this->witch->name ?></h1>
<p><em><?=$this->witch->data?></em></p>
    
<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<h3>[<?=$draft->structure->name ?>] <em><?=$draft->name ?></em></h3>
<p>
    <?php if( $draft->created ): ?>
        <em>Draft created by <?=$draft->created->actor?>: <?=$draft->created->format( \DateTimeInterface::RFC2822 )?></em>
    <?php endif; ?>
    <?php if( $draft->modified && $draft->created != $draft->modified ): ?>
        <br/> 
        <em>Draft modified by <?=$draft->modified->actor?>: <?=$draft->modified->format( \DateTimeInterface::RFC2822 )?></em>
    <?php endif; ?>
</p>

<form id="edit-action" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>Name</legend>
        <input type="text" name="name" value="<?=$draft->name ?>" />
    </fieldset>
    <div class="clear"></div>
    
    <?php foreach( $draft->attributes as $attribute ): ?>
        <fieldset>
            <legend><?=$attribute->name?> [<?=$attribute->type?>]</legend>
                <?php $attribute->edit() ?>
        </fieldset>
        <div class="clear"></div>
    <?php endforeach; ?>
    
    <?php if( $targetWitch ): ?>
        <button class="trigger-action" 
                data-action="publish"
                data-target="edit-action">
            Publish
        </button>
        <button class="trigger-action"
                data-action="save-and-return"
                data-target="edit-action">
            Save and Quit
        </button>
        <button class="trigger-action"
                data-action="save"
                data-target="edit-action">
            Save
        </button>
        <button class="trigger-action"
                data-action="delete"
                data-target="edit-action">
            Delete draft
        </button>
    <?php endif; ?>
    
    <?php if( $cancelHref ): ?>
        <button class="trigger-href" 
                data-href="<?=$cancelHref ?>">
            Cancel
        </button>
    <?php endif; ?>
</form>

    
