<?php /** @var WC\Module $this */


//$this->addCssFile('content-edit.css');
$this->addJsFile('triggers.js');
?>
<h1>
    <i class="fa fa-pencil"></i>
    <?=$this->witch->name ?>
</h1>
<p><em><?=$this->witch->data?></em></p>
    
<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<form id="edit-action" method="post" enctype="multipart/form-data">
    <input type="hidden" name="structure" value="<?=$structureName?>" />
    <h3>
        <span  id="name-display"><?=$structure->name?></span>
        <input id="name-input" type="text" name="name" value="<?=$structure->name ?>" />
    </h3>

    <em id="file-display"><?=$structure->file ?? ''?></em>
    <input id="file-input" type="text" name="file" value="<?=$structure->file ?>" />

    <div class="fieldsets-container">
        <fieldset>
            <legend>global restrictions</legend>
            <?php 
                $require    = $structure->require;
                $name       = "global";
                include $this->getIncludeDesignFile('edit/structure-require.php'); ?>
        </fieldset>
    </div>

    <div class="fieldsets-container" id="contents">
        <?php foreach( $structure->structure?->composition ?? $structure->composition ?? [] as $item ): ?>
            <fieldset>
                <legend>
                    <?=$item['name']?> 
                    <a class="up-fieldset">[&#8593;]</a>
                    <a class="down-fieldset">[&#8595;]</a>                
                    <a class="remove-fieldset">[x]</a>
                </legend>
                <ul>
                    <li>
                        <div>Name</div>
                        <input type="text" name="<?=$item['name']?>-name[]" value="<?=$item['name']?>" />
                    </li>
                    <li>
                        <div>Type</div>
                        <select class="check-restriction-toggle"
                                name="<?=$item['name']?>-type[]">
                            <?php foreach( $possibleTypes as $possibleType => $label ): ?>
                                <option <?=$possibleType === $item['type']? "selected": ""?>
                                        data-restrictions="<?=in_array($possibleType, $ingredients)? "off": "on"?>"
                                        value="<?=$possibleType?>">
                                    <?=$label?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </li>
                    <li>
                        <div>Mandatory</div>
                        <input  type="checkbox" value="1" 
                                name="<?=$item['name']?>-mandatory[]" 
                                <?=$item['mandatory'] ?? null? "checked": ""?> />
                    </li>
                    <li <?=in_array($item['type'], $ingredients)? 'style="display: none"': ''?>
                        class="structure-type-toggle">
                        <?php 
                            $require    = $item['require'] ?? [];
                            $name       = $item['name'];
                            include $this->getIncludeDesignFile('edit/structure-require.php'); ?>
                    </li>
                </ul>
            </fieldset>
        <?php endforeach; ?>
    </div>
</form>

<div class="fieldsets-container" id="new-content">
    <fieldset>
        <legend class="new-content-form">new content</legend>
        <legend>
            NEW_CONTENT_NAME
            <a class="up-fieldset">[&#8593;]</a>
            <a class="down-fieldset">[&#8595;]</a>                
            <a class="remove-fieldset">[x]</a>
        </legend>
        <ul>
            <li>
                <div>Name</div>
                <input type="text" name="NEW_CONTENT_NAME-name[]" value="" />
            </li>
            <li>
                <div>Type</div>
                <select class="check-restriction-toggle"
                        name="NEW_CONTENT_NAME-type[]">
                    <option value="0">Select new type</option>
                    <?php foreach( $possibleTypes as $possibleType => $label ): ?>
                        <option data-restrictions="<?=in_array($possibleType, $ingredients)? "off": "on"?>"
                                value="<?=$possibleType?>">
                            <?=$label?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li>
            <li>
                <div>Mandatory</div>
                <input  type="checkbox" value="1" 
                        name="NEW_CONTENT_NAME-mandatory[]" />
            </li>
            <li style="display: none"
                class="structure-type-toggle">
                <?php 
                    $require    = [];
                    $name       = "NEW_CONTENT_NAME";
                    include $this->getIncludeDesignFile('edit/structure-require.php'); ?>
            </li>
        </ul>
        <div class="new-content-actions hidden">
            <button id="add-content">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Add
            </button>
        </div>
    </fieldset>
</div>
<div class="box__actions">
    <button class="trigger-action" 
            data-action="publish"
            data-target="edit-action">
        <i class="fa fa-save" aria-hidden="true"></i>
        Save
    </button>
    <button class="trigger-href" 
            data-href="<?=$this->wc->website->getUrl('structure/view', ['structure' => $structure->name])?>">
        <i class="fa fa-times" aria-hidden="true"></i>
        Cancel
    </button>
</div>

<style>
    .restriction-settings {
        width: 100%;
    }
    .fieldsets-container {
        max-width: 700px;
        margin-top: 10px;
    }
    .fieldsets-container fieldset li {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    fieldset {
        border: 1px solid #ccc;
        box-shadow: 5px 5px 5px #ccc;
    }
        fieldset > legend {
            font-weight: bold;
        }
        fieldset.right {
            display: flex;
            justify-content: end;
            align-items: center;
        }
            fieldset.right button {
                margin-left: 8px;
            }
    #name-display,
    #file-display {
        cursor: pointer;
    }

    #name-input,
    #file-input {
        display: none;
    }

    .fieldsets-container > fieldset:first-child a.up-fieldset {
        display: none;
    }
    .fieldsets-container > fieldset:last-child a.down-fieldset {
        display: none;
    }
    .new-content-actions {
        display: flex;
        justify-content: end;
    }
    .new-content-actions.hidden {
        display: none;
    }
    #new-content legend {
        display: none;
    }
    #new-content legend.new-content-form {
        display: block;
    }
</style>

<script>
    $(document).ready(function() {

        // RESTRICTIONS 
        document.querySelectorAll("select.check-restriction-toggle").forEach( 
            (select) => select.addEventListener( 'change', (e) => checkRestrictionsToggle(e) )
        );

        function checkRestrictionsToggle( event )
        {
            let enabled =  event.target.selectedOptions[0].attributes["data-restrictions"].value === "on";
            let display = enabled? 'block': 'none';
            event.target.parentNode.parentNode.querySelector(".structure-type-toggle").style.display = display;

            return enabled;
        }

        document.querySelectorAll("select.content-add-trigger").forEach( 
            (select) => select.addEventListener( 'change', (e) => contentAddTrigger(e) )
        );

        function contentAddTrigger( event )
        {
            let candidateItem   = event.target.value;
            let candidateLabel  = event.target.selectedOptions[0].innerHTML.trim();
            let actionName      = event.target.attributes["data-target"].value;
            let items           = event.target.parentNode.parentNode.querySelectorAll('[name="'+actionName+'[]"]');

            event.target.value  =  0;

            if( Array.from(items)
                        .map( (input) => input.value )
                        .includes( candidateItem ) 
            ){  
                return false; 
            }

            let newEntry = document.createElement("a");
            newEntry.classList.add('remove-content');

            let newInput = document.createElement("input");
            newInput.setAttribute('type', 'hidden');
            newInput.setAttribute('name', actionName+'[]');
            newInput.setAttribute('value', candidateItem);

            newEntry.appendChild( newInput );

            newEntry.appendChild( document.createTextNode(' '+candidateLabel+' ') );

            let newIcon = document.createElement("i");
            newIcon.classList.add('fa');
            newIcon.classList.add('fa-times');

            newEntry.appendChild( newIcon );
            newEntry.addEventListener('click', () => newEntry.remove());
            
            let addType         = actionName.slice( actionName.lastIndexOf('-') + 1 );
            event.target.parentNode.parentNode.querySelector('.'+addType+"-contents-container").append( newEntry );

            return true;
        }

        document.querySelectorAll(".remove-content").forEach( 
            (elmt) => elmt.addEventListener('click', () => elmt.remove())
        );

        // NAME / FILE (hidden inputs)
        ['name', 'file'].forEach( (hiddenInput) => {

            document.querySelector('#'+hiddenInput+'-display').addEventListener('click', () => {
                document.querySelector('#'+hiddenInput+'-input').style.display = 'block';
                document.querySelector('#'+hiddenInput+'-input').focus();
                document.querySelector('#'+hiddenInput+'-display').style.display = 'none';
            });

            document.querySelector('#'+hiddenInput+'-input').addEventListener('focusout', () => {
                document.querySelector('#'+hiddenInput+'-input').style.display = 'none';
                document.querySelector('#'+hiddenInput+'-display').style.display = 'block';
            });

            document.querySelector('#'+hiddenInput+'-input').addEventListener('change', () => {
                let value = document.querySelector('#'+hiddenInput+'-input').value;

                if( value !== '' ){
                    document.querySelector('#'+hiddenInput+'-display').innerHTML = value;
                }
                else {
                    document.querySelector('#'+hiddenInput+'-input').value = document.querySelector('#'+hiddenInput+'-display').innerHTML;
                }

                document.querySelector('#'+hiddenInput+'-input').style.display = 'none';
                document.querySelector('#'+hiddenInput+'-display').style.display = 'block';
            });
        });

        // FIELDSETS
        $("fieldset > legend > a.remove-fieldset").click(function(){
            if( confirm('Confirm Remove') ){
                $(this).parent('legend').parent('fieldset').remove();
            }
        });

        $("fieldset > legend > a.up-fieldset").click(function(){
            let index       = $(this).parent('legend').parent('fieldset').index();
            if( index === 0 ){
                return;
            }

            $(this).parent('legend').parent('fieldset').insertBefore( 
                $(this).parent('legend').parent('fieldset').prev() 
            );
        });

        $("fieldset > legend > a.down-fieldset").click(function(){
            $(this).parent('legend').parent('fieldset').insertAfter( 
                $(this).parent('legend').parent('fieldset').next() 
            );
        });

        $("fieldset.structure > ul > li > h4 > a.remove-fieldset-element").click(function(){
            if( confirm('Confirm Remove') ){
                $(this).parent('h4').parent('li').remove();
            }
        });

        document.querySelector('[name="NEW_CONTENT_NAME-name[]"]').addEventListener( 'input', (e) => {
            let name = e.target.value;
            document.querySelector('.new-content-actions').classList.remove('hidden');
            if( name === '' ){
                document.querySelector('.new-content-actions').classList.add('hidden');
            }
        });

        //$('#add-content').click( function() {
        document.querySelector('#add-content').addEventListener("click", function() {

            let fieldset    = document.getElementById('new-content').getElementsByTagName('fieldset')[0]; 
            let name        = document.querySelector('[name="NEW_CONTENT_NAME-name[]"]').value;
            let type        = document.querySelector('[name="NEW_CONTENT_NAME-type[]"]').value;

            let newElement  = fieldset.cloneNode(true);

            newElement.querySelector('legend.new-content-form').remove();
            newElement.querySelector('.new-content-actions').remove();

            //newElement.innerHTML = newElement.innerHTML.replace('NEW_CONTENT_NAME',name)
            newElement.querySelector('legend').innerHTML = newElement.querySelector('legend').innerHTML.replace('NEW_CONTENT_NAME',name)

            newElement.querySelector('[name="NEW_CONTENT_NAME-name[]"]').setAttribute('name', name+'-name[]');
            newElement.querySelector('[name="NEW_CONTENT_NAME-type[]"]').setAttribute('name', name+'-type[]');
            newElement.querySelector('[name="NEW_CONTENT_NAME-mandatory[]"]').setAttribute('name', name+'-mandatory[]');

            //console.log(name, type);
            //console.log(newElement);
            //console.log(newElement.innerHTML.replace("NEW_CONTENT_NAME", name));

            //$('#contents').append( newElement );
           // document.querySelector('#contents').append( newElement );

            document.querySelector('#contents').append( newElement );
        });
    });
</script>