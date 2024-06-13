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
        [<?=$structure->type?>] 
        <em  id="name-display"><?=$structure->name?></em>
        <input id="name-input" type="text" name="name" value="<?=$structure->name ?>" />
    </h3>

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
    .fieldsets-container {
        max-width: 700px;
        margin-top: 10px;
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
    #name-display {
        cursor: pointer;
    }

    #name-input {
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