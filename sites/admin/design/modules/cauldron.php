<?php   
/** @var WC\Module $this 
  * @var WC\Cauldron $draft */

$this->addCssFile('content-edit.css');
$this->addJsFile('triggers.js');
//$this->addJsFile('jquery-ui.min.js');
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
                    <em id="name-display"><?=$content->name ?></em>
                    <input id="name-input" type="text" name="name" value="<?=$content->name ?>" />


                    <span><?=$content->name?> [<?=$content->type?>]</span>
                    <?=$content->getInputName(false)?>

                    
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
    #name-display {
        cursor: pointer;
    }

    #name-input {
        display: none;
    }
    .fieldsets-container > fieldset:first-child > *:first-child > a.up-fieldset {
        display: none;
    }
    .fieldsets-container > fieldset:last-child > *:first-child > a.down-fieldset {
        display: none;
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

        document.querySelectorAll("fieldset.structure > ul > li > h4 > a.remove-fieldset-element").forEach( 
            (anchor) => anchor.addEventListener( 'click', () => removeFieldSet(anchor) )
        );
        
        /*
        document.querySelectorAll("a.remove-fieldset").forEach( 
            (anchor) => anchor.addEventListener( 'click', () => removeFieldSet(anchor) )
        );*/


        function removeFieldSet( anchor )
        {
            console.log('removeFieldSet');

            if( confirm('Confirm Remove') ){
                anchor.parentNode.parentNode.remove();
            }

            return;
        }
        
        document.querySelectorAll("fieldset a.up-fieldset").forEach( 
            (anchor) => anchor.addEventListener( 'click', () => upFieldSet(anchor) )
        );

        document.querySelectorAll("fieldset.structure > ul > li > h4 > a.up-fieldset-element").forEach( 
            (anchor) => anchor.addEventListener( 'click', () => upFieldSet(anchor) )
        );

        function upFieldSet( anchor )
        {
            console.log('upFieldSet');

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

        document.querySelectorAll("fieldset.structure > ul > li > h4 > a.down-fieldset-element").forEach( 
            (anchor) => anchor.addEventListener( 'click', () => downFieldSet(anchor) )
        );

        function downFieldSet( anchor )
        {
            console.log('downFieldSet');

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
        
        document.querySelector('#name-display').addEventListener('click', () => {
            document.querySelector('#name-input').style.display = 'inline-block';
            document.querySelector('#name-input').focus();
            document.querySelector('#name-display').style.display = 'none';
        });

        document.querySelector('#name-input').addEventListener('focusout', () => {
            document.querySelector('#name-input').style.display = 'none';
            document.querySelector('#name-display').style.display = 'inline-block';
        });

        document.querySelector('#name-input').addEventListener('change', () => {
            let value = document.querySelector('#name-input').value;

            if( value !== '' ){
                document.querySelector('#name-display').innerHTML = value;
            }
            else {
                document.querySelector('#name-input').value = document.querySelector('#name-display').innerHTML;
            }

            document.querySelector('#name-input').style.display = 'none';
            document.querySelector('#name-display').style.display = 'inline-block';
        });


    });
</script>