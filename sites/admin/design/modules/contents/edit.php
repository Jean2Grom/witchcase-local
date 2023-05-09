<style>
    h2 {
        font-size: 1.3em;
        font-style: italic;
        font-weight: normal;
        margin-bottom: 20px;
    }
    fieldset {
        min-width: 30%;
        border-radius: 15px;
        margin-bottom: 15px;
        float: left;
        margin-right: 15px;
        padding: 20px 10px;
        border: 1px solid #ccc;
        box-shadow: 5px 5px 5px #ccc;
    }
        fieldset legend {
            font-weight: bold;
        }
        fieldset p {
            font-style: italic;
            margin-top: 20px;
        }
        fieldset input[type=text],
        fieldset textarea {
            width: 95%;
        }
    .fieldsets-container {
        display: table;
        float: left;
    }
    textarea {
        width: 350px;
        height: 100px;
        resize: none;
    }
    input[type=number]{
        width: 60px;
    }
    input#customUrl {
        width: 95%;
    }
    label{
        font-weight: bold;
        font-style: normal;
    }
    .site_selected {
        display: none;
    }
    .custom-url_selected {
        /*display: none;*/
    }
    
    
</style>

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

    
<script>
$(document).ready(function()
{    
    $('.trigger-href').click(function(){
        window.location.href = $(this).data('href');
        return false;
    });
    
    $('.trigger-action').click(function(){
        let actionName  = $(this).data('action');
        let targetId    = $(this).data('target');
        if( actionName === undefined || targetId === undefined ){
            return false;
        }
        
        let action = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( actionName );
        
        $('#'+targetId).append( action );        
        $('#'+targetId).submit();
        
        return false;
    });
});
</script>    