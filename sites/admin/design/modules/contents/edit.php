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
<h1>
    Edition du contenu
    <?php if( $target ): ?>
        :
        <?=$target->structure->name ?>
    <?php endif; ?>
</h1>

<?php if( $target ): ?>
    <h2>
        <?=$target->name ?>
    </h2>
<?php endif; ?>

<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<form id="edit-action" method="post" enctype="multipart/form-data">
    <?php foreach( $target->attributes as $attribute ): ?>
        <fieldset>
            <legend><?=$attribute->name?> [<?=$attribute->type?>]</legend>
                <?php $attribute->edit() ?>
        </fieldset>
        <div class="clear"></div>
    <?php endforeach; ?>
    
    <?php if( $targetWitch ): ?>
        <button class="" 
                id="save-content-and-return-action">
            Sauvegarder et Quitter
        </button>
        <button class="" 
                id="save-content-action">
            Sauvegarder
        </button>
    <?php endif; ?>
    
    <?php if( $cancelHref ): ?>
        <button class="" 
                id="cancel"
                data-href="<?=$cancelHref ?>">
            Annuler
        </button>
    <?php endif; ?>
</form>

    
<script>
$(document).ready(function()
{
    $('#cancel').click(function(){
        window.location.href = $(this).data('href');
        return false;
    });
    
    $('#save-content-action').click(function(){
        return save( "save-content" );
    });

    $('#save-content-and-return-action').click(function(){
        return save( "save-content-and-return" );
    });
    
    function save( actionName )
    {
        let action = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( actionName );
        
        $('#edit-action').append( action );
        
        return true;
    }

});
</script>    