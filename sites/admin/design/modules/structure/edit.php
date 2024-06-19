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
                $this->wc->debug($require);
                $name       = "global";
                include $this->getIncludeDesignFile('edit/structure-require.php'); ?>
        </fieldset>
    </div>

    <div class="fieldsets-container">
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
                                data-target="<?=$item['name']?>-structure-type-toggle"
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
                                name="<?=$item['name']?>-mandatory[] ?>" 
                                <?=$item['mandatory'] ?? null? "checked": ""?> />
                    </li>
                    <li <?=in_array($item['type'], $ingredients)? 'style="display: none"': ''?>
                        id="<?=$item['name']?>-structure-type-toggle">
                        <?php 
                            $require    = $item['require'] ?? [];
                            $name       = $item['name'];
                            include $this->getIncludeDesignFile('edit/structure-require.php'); ?>
                    </li>
                </ul>
            </fieldset>
        <?php endforeach; ?>
    </div>
    <div class="fieldsets-container">
        <fieldset class="right">
            <select id="add-content-type">
                <?php foreach( $possibleTypes as $possibleType => $label ): ?>
                    <option value="<?=$possibleType?>">
                        <?=$label?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button id="add-content">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Add
            </button>
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
</form>


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
    }

    fieldset {
        border: 1px solid #ccc;
        box-shadow: 5px 5px 5px #ccc;
    }
        fieldset > legend {
            font-weight: bold;
        }
        fieldset > ul > li  {
            display: flex;
            justify-content: space-between;
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
</style>

<script>
    $(document).ready(function() {

        // RESTRICTIONS 
        $('.check-restriction-toggle').change((e) => {
            let idRestrictionDom = e.target.attributes["data-target"].value;
            let enabled          = $(e.target).find('option:selected').data('restrictions') === "on";

            if( enabled ){
                $('#'+idRestrictionDom).show();
            }
            else {
                $('#'+idRestrictionDom).hide();
            }
        });

        $('.content-add-trigger').change((e) => {
            let candidateItem   = e.target.value;
            let candidateLabel  = $(e.target).find('option:selected').html().trim();
            let actionName      = e.target.attributes["data-target"].value;
            let items           = document.querySelectorAll('[name="'+actionName+'[]"]');

            $(e.target).val( 0 );

            if( Array.from(items)
                        .map( (input) => input.value )
                        .includes( candidateItem ) 
            ){  return; }

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

            $('#'+actionName+'-contents').append( newEntry );
        });

        $('.restriction-settings').on('click', '.remove-content', function(){
            $(this).remove();
        });        

        // NAME / FILE (hidden inputs)
        ['name', 'file'].forEach((hiddenInput) => {
            $('#'+hiddenInput+'-display').click(function(){
                $('#'+hiddenInput+'-input').show().focus();
                $('#'+hiddenInput+'-display').hide();
            });

            $('#'+hiddenInput+'-input').on('focusout', function(){
                $('#'+hiddenInput+'-input').hide();
                $('#'+hiddenInput+'-display').show();
            });

            $('#'+hiddenInput+'-input').change(function(){
                $('#'+hiddenInput+'-display').html( $('#'+hiddenInput+'-input').val() );
                $('#'+hiddenInput+'-input').hide();
                $('#'+hiddenInput+'-display').show();
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

    });
</script>