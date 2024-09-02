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
        <span   class="span-input-toggle" 
                data-input="__name" 
                data-value="<?=$draft->name ?>"><?=$draft->name ?></span>
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
                    <span   class="span-input-toggle" 
                            data-input="<?=$content->getInputName()?>__name" 
                            data-value="<?=$content->name ?>"><?=$content->name ?></span>

                    [<?=$content->type?>]
                    
                    <a class="up-fieldset">[&#8593;]</a>
                    <a class="down-fieldset">[&#8595;]</a>
                    <a class="remove-fieldset">[x]</a>
                </legend>
                <?php $content->edit( 
                    null, 
                    [
                        'ingredients'   => $ingredients, 
                        'structures'    => $structures,
                        'inputName'     => $content->getInputName(),
                    ]
                ); ?>
            </fieldset>
        <?php endforeach; ?>
        
        <?php $this->include(
            'cauldron/add.php', 
            [ 
                'ingredients'   => $ingredients, 
                'structures'    => $structures, 
                'inputName'     => '',
            ]
        );?>
    </div>    
</form>

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
        fieldset > .fieldsets-container > fieldset.structure {
            box-shadow: none;
            border: 1px solid #444;
            border-radius: 0;
        }
            fieldset > .fieldsets-container > fieldset.structure .cauldron-add-actions .add-form {
                box-shadow: none;
                border: 1px solid #444;
                border-radius: 0;
            }
    fieldset.structure.integration-0 {
        background-color: #ddd;
    }
    fieldset.structure.integration-1 {
        background-color: #ccc;
    }
    fieldset.structure.integration-2 {
        background-color: #bbb;
    }
    fieldset.structure.integration-3 {
        background-color: #aaa;
    }

    .cauldron-add-actions {
        display: flex;
        align-items: center;
        flex-direction: column;
        justify-content: center;
        justify-items: initial;
        width: 100%;
    }
    .cauldron-add-actions .add-form {
        display: none;
        width: auto;
        padding: 16px;
        flex-direction: column;
    }
        .cauldron-add-actions .add-form legend {
            margin: auto;
        }
        .cauldron-add-actions .add-form input {
            margin-top: 8px;
        }
        .cauldron-add-actions .add-form button {
            box-shadow: inherit;
            border-radius: inherit;
            margin: 8px auto 4px;
        }
        .cauldron-add-actions .add-form button.disabled {
            cursor: not-allowed;
            opacity: 0.75;
            font-style: italic;
        }
    .cauldron-add-actions .show-form {
        border-radius: 24px;
    }

    #edit-action {
        margin-bottom: 24px;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", () => {

        document.querySelectorAll(".cauldron-add-actions").forEach( 
            container => {
                let showButton      = container.querySelector(".show-form");
                let form            = container.querySelector(".add-form");
                let saveButton      = form.querySelector("button");
                let typeSelector    = form.querySelector("select");
                let nameInput       = form.querySelector("input");

                showButton.addEventListener( 'click', e => {
                    e.preventDefault();
                    
                    showButton.style.display    = 'none';
                    form.style.display          = 'flex';
                    form.focus();
                    
                    return false;
                });

                form.querySelector("a.hide-form").addEventListener('click', () => {
                    cancelAddForm(form, showButton);
                });

                document.addEventListener('click', function(e){
                    if( form.style.display !== 'none' 
                        && !form.contains(e.target) 
                        && !showButton.contains(e.target) 
                    ){
                        cancelAddForm(form, showButton);
                    }                    
                });

                saveButton.addEventListener('click', e => {
                    e.preventDefault();
                    
                    if( !saveButton.classList.contains("disabled") )
                    {
                        let action = document.createElement('input');
                        action.setAttribute('type', "hidden");
                        action.setAttribute('name', "action");
                        action.value = saveButton.dataset.action;

                        let actionForm = document.querySelector('#' + saveButton.dataset.target);
                        actionForm.append(action);
                        actionForm.submit();
                    }
                    return false;
                });

                typeSelector.addEventListener('change', () => checkAddFormValidity( typeSelector, nameInput, saveButton ));
                nameInput.addEventListener('input', () => checkAddFormValidity( typeSelector, nameInput, saveButton ));
            }
        );

        function checkAddFormValidity( select, input, button )
        {
            if( !button.classList.contains("disabled") ){
                button.classList.add("disabled");
            }
            if( select.value !== "" && input.value.trim() !== '' ){
                button.classList.remove("disabled");
            }
        }

        function cancelAddForm(form, showButton)
        {
            form.querySelector('select').value  = "";
            form.querySelector('input').value   = "";
            showButton.style.display    = 'block';
            form.style.display          = 'none';
        }

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
                
                let oldInput = document.querySelector(
                    'input[name="' 
                    + span.dataset.input
                    + '"]'
                );

                if( oldInput ){
                    oldInput.remove();
                }
                
                let input       = document.createElement('input');
                input.setAttribute('type', "text");
                input.setAttribute('name', span.dataset.input);
                input.value     = span.innerHTML.trim();

                span.append(input);
                span.parentNode.insertBefore(input, span);
                span.style.display  = 'none';
                input.style.display = 'inline-block';
                input.focus();


                input.addEventListener('focusout', () => checkSpanInputToggleValidity( input, span ));
                input.addEventListener('change', () => checkSpanInputToggleValidity( input, span ));

            })
        );

        function checkSpanInputToggleValidity(input, span)
        {
            if( input.value === '' || input.value === span.dataset.value )
            {
                span.innerHTML = span.dataset.value;
                input.remove();
            }
            else 
            {
                span.innerHTML      = input.value;
                input.style.display = 'none';
            }

            span.style.display  = 'inline-block';
        }

    });
</script>