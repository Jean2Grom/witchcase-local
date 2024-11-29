<?php /** @var WC\Module $this */
$this->addCssFile('content-edit.css');
$this->addJsFile('triggers.js');

/** @var ?WC\Cauldron $cauldron */
$cauldron = $cauldron ?? $this->witch("target")->cauldron();
?>

<h1>
    <i class="fa fa-feather-alt"></i>
    <?=$this->witch("target")->name ?>
</h1>
<p><em><?=$this->witch("target")->data?></em></p>
    
<?php $this->include('alerts.php', ['alerts' => $this->wc->user->getAlerts()]); ?>

<form id="edit-action" method="post" enctype="multipart/form-data">
    <?php if( $cauldron->draft()->exist() ): ?>
        <input  type="hidden" 
                name="ID" value="<?=$cauldron->draft()->id ?>" />
    <?php endif; ?>
    <h3>
        [<?=$cauldron->recipe ?>] 
        <span   class="span-input-toggle" 
                data-input="name" 
                data-value="<?=$cauldron->draft()->name ?>"><?=$cauldron->draft()->name ?></span>
    </h3>
    <p>
        <?php if( $cauldron->draft()->created ): ?>
            <em>Draft created by <?=$cauldron->draft()->created->actor?>: <?=$cauldron->draft()->created->format( \DateTimeInterface::RFC2822 )?></em>
        <?php endif; ?>
        <?php if( $cauldron->draft()->modified && $cauldron->draft()->created != $cauldron->draft()->modified ): ?>
            <br/> 
            <em>Draft modified by <?=$cauldron->draft()->modified->actor?>: <?=$cauldron->draft()->modified->format( \DateTimeInterface::RFC2822 )?></em>
        <?php endif; ?>
    </p>
    
    <div class="fieldsets-container">
        <?php foreach( $cauldron->draft()->contents() as $content ): 
            $contentInput = "content[".$content->getInputIndex()."]";
            ?>
            <fieldset class="<?=$content->isCauldron()? 'cauldron': 'ingredient'?>">
                <legend>
                    <span   class="span-input-toggle" 
                            data-input="<?=$contentInput?>[name]" 
                            data-value="<?=$content->name ?>"><?=$content->name ?></span>

                    [<?=$content->type?>]
                    
                    <a class="up-fieldset">[&#8593;]</a>
                    <a class="down-fieldset">[&#8595;]</a>
                    <a class="remove-fieldset">[x]</a>
                </legend>
                <input  type="hidden" 
                        name="<?=$contentInput ?>[type]" value="<?=$content->type ?>" />
                <?php $content->edit( 
                    null, 
                    [ 'input' => $contentInput ]
                ); ?>
            </fieldset>
        <?php endforeach; ?>        
    </div>
    
    <?php $this->include(
        'cauldron/add.php', 
        [
            'input'     => "content[new]",
            'cauldron'  => $cauldron->draft()
        ]
    ); ?>
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

<button class="trigger-href" 
        data-href="<?= $this->wc->website->getUrl('view', [ 'id' => $this->witch("target")->id ]) ?>">
    <i class="fa fa-times"></i>
    Cancel
</button>

<style>
    span.span-input-toggle {
        cursor: pointer;
    }
    input.span-input-toggle {
        display: none;
    }
    fieldset:first-of-type > *:first-child > a.up-fieldset {
        display: none;
    }
     fieldset:last-of-type > *:first-child > a.down-fieldset {
        display: none;
    }
    fieldset.cauldron {
        background-color: #eee;
    }
    fieldset.ingredient {
        background-color: #fff;
    }
    fieldset > .fieldsets-container {
        margin-left: 24px;
    }
        fieldset > .fieldsets-container > fieldset.cauldron {
            box-shadow: none;
            border: 1px solid #444;
            border-radius: 0;
        }
            fieldset > .fieldsets-container > fieldset.cauldron .cauldron-add-actions .add-form {
                box-shadow: none;
                border: 1px solid #444;
                border-radius: 0;
            }
    fieldset.cauldron.integration-0 {
        background-color: #ddd;
    }
    fieldset.cauldron.integration-1 {
        background-color: #ccc;
    }
    fieldset.cauldron.integration-2 {
        background-color: #bbb;
    }
    fieldset.cauldron.integration-3 {
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
        border-radius: 5px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        box-shadow: 5px 5px 5px #ccc;
    }
        .cauldron-add-actions .add-form h4 {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
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

                        let inputName = document.createElement('input');
                        inputName.setAttribute('type', "hidden");
                        inputName.setAttribute('name', form.dataset.input + '[name]' );
                        inputName.value = nameInput.value;
                        
                        let inputType = document.createElement('input');
                        inputType.setAttribute('type', "hidden");
                        inputType.setAttribute('name', form.dataset.input + '[type]' );
                        inputType.value = typeSelector.value;
                        
                        let actionForm = document.querySelector('#' + saveButton.dataset.target);
                        actionForm.append(inputName);
                        actionForm.append(inputType);
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