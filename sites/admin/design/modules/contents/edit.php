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
<?php if( $target ): ?>
    <h2><em><?=$target->name ?></em></h2>
    <h3>[<?=$target->structure->name ?>]</h3>
<?php endif; ?>

<p><em><?=$this->witch->data?></em></p>
    
<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<form id="edit-action" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>Nom</legend>
        <input type="text" name="name" value="<?=$target->name ?>" />
    </fieldset>
    <div class="clear"></div>
    
    <?php foreach( $target->attributes as $attribute ): ?>
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
                data-action="save-content-and-return"
                data-target="edit-action">
            Sauvegarder et Quitter
        </button>
        <button class="trigger-action"
                data-action="save-content"
                data-target="edit-action">
            Sauvegarder
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