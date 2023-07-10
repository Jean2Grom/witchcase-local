<?php
    $this->addCssFile('content-edit.css');
    $this->addJsFile('triggers.js');
?>
<h1><?=$this->witch->name ?></h1>
<p><em><?=$this->witch->data?></em></p>

<h3>[<?=$draft->structure->name ?>] <em><?=$draft->name ?></em></h3>
<p>
    <?php if( $draft->created ): ?>
        <em>Créé le <?=$draft->created->frenchFormat( true )?> par <?=$draft->created->actor?></em>
        <?php if( $draft->modified && $draft->created != $draft->modified ): ?>
            <br/> 
            <em>Modifié le <?=$draft->modified->frenchFormat( true )?> par <?=$draft->modified->actor?></em>
        <?php endif; ?>
    <?php endif; ?>    
</p>
    
<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<form id="edit-action" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>Nom</legend>
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
            Publier
        </button>
        <button class="trigger-action"
                data-action="save-and-return"
                data-target="edit-action">
            Sauvegarder et Quitter
        </button>
        <button class="trigger-action"
                data-action="save"
                data-target="edit-action">
            Sauvegarder
        </button>
        <button class="trigger-action"
                data-action="delete"
                data-target="edit-action">
            Supprimer le brouillon
        </button>
    <?php endif; ?>
    
    <?php if( $cancelHref ): ?>
        <button class="trigger-href" 
                data-href="<?=$cancelHref ?>">
            Annuler
        </button>
    <?php endif; ?>
</form>

    
