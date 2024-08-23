<?php   
/** @var WC\Module $this 
  * @var WC\Cauldron $draft */

$this->addCssFile('content-edit.css');
$this->addJsFile('triggers.js');
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
        <span class="span-input-toggle" data-name="name"><?=$draft->name ?></span>
        <input class="span-input-toggle" type="text" name="name" value="<?=$draft->name ?>" />
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
                    <span class="span-input-toggle" data-name="<?=$content->getInputName(false)?>-name"><?=$content->name ?></span>
                    <input class="span-input-toggle" type="text" name="<?=$content->getInputName(false)?>-name" value="<?=$content->name ?>" />

                    [<?=$content->type?>]
                    
                    <a class="up-fieldset">[&#8593;]</a>
                    <a class="down-fieldset">[&#8595;]</a>
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
    span.span-input-toggle {
        cursor: pointer;
    }
    input.span-input-toggle {
        display: none;
    }
    .fieldsets-container > fieldset:first-child > *:first-child > a.up-fieldset {
        display: none;
    }
    .fieldsets-container > fieldset:last-child > *:first-child > a.down-fieldset {
        display: none;
    }
    fieldset.structure {
        background-color: #eee;
    }
    fieldset.ingredient {
        background-color: #fff;
    }
    fieldset > .fieldsets-container {
        margin-left: 24px;
    }
</style>
<script>
    $(document).ready(function() {

        document.querySelectorAll("fieldset a.remove-fieldset").forEach( 
            (anchor) => anchor.addEventListener( 'click', () => removeFieldSet(anchor) )
        );

        function removeFieldSet( anchor )
        {
            if( confirm('Confirm Remove') ){
                anchor.parentNode.parentNode.remove();
            }

            return;
        }
        
        document.querySelectorAll("fieldset a.up-fieldset").forEach( 
            (anchor) => anchor.addEventListener( 'click', () => upFieldSet(anchor) )
        );

        function upFieldSet( anchor )
        {
            let fieldset        = anchor.parentNode.parentNode;
            let container       = fieldset.parentNode;
            let fieldsetArray   = Array.from(fieldset.parentNode.childNodes).filter( element => element.type === fieldset.type );
            let index           = fieldsetArray.indexOf(fieldset);

            if( index === 0 ){
                return;
            }

            let targetPosition  = Array.prototype.slice.call( container.childNodes ).indexOf( fieldsetArray[index - 1] );    

            container.insertBefore(fieldset, container.childNodes[ targetPosition ] );
            return;
        }

        document.querySelectorAll("fieldset a.down-fieldset").forEach( 
            (anchor) => anchor.addEventListener( 'click', () => downFieldSet(anchor) )
        );

        function downFieldSet( anchor )
        {
            let fieldset        = anchor.parentNode.parentNode;
            let container       = fieldset.parentNode;
            let fieldsetArray   = Array.from(fieldset.parentNode.childNodes).filter( element => element.type === fieldset.type );
            let index           = fieldsetArray.indexOf(fieldset);

            if( index+1 === fieldsetArray.length ){
                return;
            }

            let target  = fieldsetArray[index + 2];
            if( target === undefined )
            {
                container.appendChild( fieldset );
                return;
            }

            let targetPosition  = Array.prototype.slice.call( container.childNodes ).indexOf( target );    

            container.insertBefore(fieldset, container.childNodes[ targetPosition ] );
            return;
        }
        
        document.querySelectorAll('span.span-input-toggle').forEach(
            span => span.addEventListener('click', () => {
                let input       = document.querySelector(
                    'input.span-input-toggle[name="' 
                    + span.attributes['data-name'].value
                    + '"]'
                );                
                span.style.display  = 'none';
                input.style.display = 'inline-block';
                input.focus();
            })
        );

        document.querySelectorAll('input.span-input-toggle').forEach(
            input => {
                input.addEventListener('focusout', () => {
                    let span       = document.querySelector(
                        'span.span-input-toggle[data-name="' 
                        + input.name
                        + '"]'
                    );

                    span.style.display  = 'inline-block';
                    input.style.display = 'none';
                });

                input.addEventListener('change', () => {
                    let span       = document.querySelector(
                        'span.span-input-toggle[data-name="' 
                        + input.name
                        + '"]'
                    );

                    if( input.value !== '' ){
                        span.innerHTML = input.value;
                    }
                    else {
                        input.value = span.innerHTML;
                    }

                    input.style.display = 'none';
                    span.style.display  = 'inline-block';
                });
            }
        );

    });
</script>