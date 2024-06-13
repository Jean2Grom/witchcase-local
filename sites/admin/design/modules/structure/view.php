<?php /** @var WC\Module $this */

$this->addCssFile('content-edit.css');
$this->addJsFile('triggers.js');
?>
<h1>
    <i class="fa fa-eye"></i>
    <?=$this->witch->name ?>
</h1>
<p><em><?=$this->witch->data?></em></p>
    
<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<h3>[<?=$structure->type?>] <em><?=$structure->name?></em></h3>

<div class="fieldsets-container" style="max-width: 700px;margin-top: 10px;">
    <?php foreach( $structure->structure?->composition ?? $structure->composition ?? [] as $item ): ?>
        <fieldset>
            <legend><?=$item['name']?></legend>
            <ul>
                <!--li style="display: flex;justify-content: space-between;">
                    <div>Name</div>
                    <div><?=$item['name']?></div>
                </li-->
                <li style="display: flex;justify-content: space-between;">
                    <div>Type</div>
                    <div><?=$item['type']?></div>
                </li>
                <li style="display: flex;justify-content: space-between;">
                    <div>Mandatory</div>
                    <div><?=$item['mandatory'] ?? null? "true": "false"?></div>
                </li>
            </ul>
        </fieldset>
    <?php endforeach; ?>
</div>
    
<div class="box__actions">
    <button class="trigger-href" 
            data-href="#" 
            id="cauldron__edit">
        <i class="fa fa-pencil" aria-hidden="true"></i>
        Edit
    </button>
    <button class="trigger-href" 
            data-href="<?=$this->wc->website->getUrl('structure')?>">
        <i class="fa fa-list" aria-hidden="true"></i>
        Back
    </button>
</div>
