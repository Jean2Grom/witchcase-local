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

    <ul  class="global-data">
        <li>
            <div>Accepted contents</div>
            <select id="add-accepted">
                <option value="0">Select content type</option>
                <?php foreach( $possibleTypes as $possibleType => $label ): ?>
                    <option value="<?=$possibleType?>">
                        <?=$label?>
                    </option>
                <?php endforeach; ?>                
            </select>
        </li>
        <li>
            <ul>
                <?php foreach( $structure->require['accept'] ?? [] as $acceptedItem ): ?>
                    <?=$acceptedItem?>
                    <a><i class="fa fa-times"></i></a>
                <?php endforeach; ?> 
            </ul>
        </li>
        <li>
            <div>Refused contents</div>
            <select id="add-refused">
                <option value="0">Select content type</option>
                <?php foreach( $possibleTypes as $possibleType => $label ): ?>
                    <option value="<?=$possibleType?>">
                        <?=$label?>
                    </option>
                <?php endforeach; ?>                
            </select>
        </li>
        <li>
            <ul>
                <?php foreach( $structure->require['refuse'] ?? [] as $refusedItem ): ?>
                    <?=$refusedItem?>
                    <a><i class="fa fa-times"></i></a>
                <?php endforeach; ?> 
            </ul>
        </li>
        <li>
            <div>Minimum required</div>
            <div><input type="number" min="0" value="<?=$structure->require['min'] ?? 0?>" /></div>
        </li>
        <li>
            <div>Maximum allowed</div>
            <div><input type="number" min="0" value="<?=$structure->require['max'] ?? 0?>" /></div>
        </li>
    </ul>

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
                        <input type="text" name="content-name[]" value="<?=$item['name']?>" />
                    </li>
                    <li>
                        <div>Type</div>
                        <select name="content-type[]">
                            <?php foreach( $possibleTypes as $possibleType => $label ): ?>
                                <option <?=$possibleType === $item['type']? "selected": ""?>
                                        value="<?=$possibleType?>">
                                    <?=$label?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </li>
                    <li>
                        <div>Mandatory</div>
                        <input  type="checkbox" value="1" 
                                name="content-mandatory[] ?>" 
                                <?=$item['mandatory'] ?? null? "checked": ""?> />
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
    .global-data, 
    .fieldsets-container {
        max-width: 700px;
        margin-top: 10px;
    }
    ul.global-data li,
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