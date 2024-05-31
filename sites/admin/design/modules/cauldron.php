<?php   /** @var WC\Module $this @var WC\Cauldron $draft */

$this->addCssFile('content-edit.css');
$this->addJsFile('triggers.js');
$this->addJsFile('jquery-ui.min.js');
?>
<h1>
    <i class="fa fa-feather-alt"></i>
    <?=$this->witch("target")->name ?>
</h1>
<p><em><?=$this->witch("target")->data?></em></p>
    
<?php include $this->getIncludeDesignFile('alerts.php'); ?>


<form id="edit-action" method="post" enctype="multipart/form-data">
    <h3>
        [<?=$draft->data->structure ?>] 
        <em id="name-display"><?=$draft->name ?></em>
        <input id="name-input" type="text" name="name" value="<?=$draft->name ?>" />
    </h3>
    <p>
        <?php if( $draft->created ): ?>
            <em>Draft created by <?=$draft->created->actor?>: <?=$draft->created->format( \DateTimeInterface::RFC2822 )?></em>
        <?php endif; ?>
        <?php if( $draft->modified && $draft->created != $draft->modified ): ?>
            <br/> 
            <em>Draft modified by <?=$draft->modified->actor?>: <?=$draft->modified->format( \DateTimeInterface::RFC2822 )?></em>
        <?php endif; ?>
    </p>
    
    <div class="fieldsets-container">
        <?php foreach( $draft->content() as $content ): ?>
            <fieldset class="<?=$content->isStructure()? 'structure': 'ingredient'?>">
                <legend>
                    <span><?=$content->name?> [<?=$content->type?>]</span>
                    <a class="remove-fieldset">[x]</a>
                </legend>
                <?php $content->edit() ?>
            </fieldset>
        <?php endforeach; ?>
    </div>
    
    <?php if( $this->witch("target") ): ?>
        <button class="trigger-action" 
                data-action="publish"
                data-target="edit-action">
            <i class="fa fa-check"></i>
            Publish
        </button>
        <button class="trigger-action"
                data-action="save-and-return"
                data-target="edit-action">
            <i class="fa fa-share"></i>
            Save and Quit
        </button>
        <button class="trigger-action"
                data-action="save"
                data-target="edit-action">
            <i class="fa fa-save"></i>
            Save
        </button>
        <button class="trigger-action"
                data-action="delete"
                data-target="edit-action">
            <i class="fa fa-trash"></i>
            Delete draft
        </button>
    <?php endif; ?>
    
    <?php if( $cancelHref ): ?>
        <button class="trigger-href" 
                data-href="<?=$cancelHref ?>">
            <i class="fa fa-times"></i>
            Cancel
        </button>
    <?php endif; ?>
</form>

<style>
    #name-display {
        cursor: pointer;
    }

    #name-input {
        display: none;
    }

    fieldset.ingredient {
        border: none;
        box-shadow: 1px 1px 1px #ddd;
        background-color: #fcfcfc;
    }
    .fieldsets-container.ui-sortable > fieldset.ingredient {
        cursor: move;
    }
</style>
<script>
    $(document).ready(function() {

        $('#name-display').click(function(){
            $('#name-input').show();
            $('#name-display').hide();
        });

        $('#name-input').on('focusout', function(){
            $('#name-input').hide();
            $('#name-display').show();
        });

        $('#name-input').change(function(){
            $('#name-display').html( $('#name-input').val() );
            $('#name-input').hide();
            $('#name-display').show();
        });

        $(".fieldsets-container").sortable();
        //$(".fieldsets-container fieldset > ul").sortable();
        $("fieldset > legend > a.remove-fieldset").click(function(){
            $(this).parent('legend').parent('fieldset').remove();
        });
    });
</script>