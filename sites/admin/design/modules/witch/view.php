<?php /** @var WC\Module $this */

$this->addCssFile('view.css');
$this->addCssFile('boxes.css');
$this->addJsFile('triggers.js');
$this->addJsFile('view.js');

$this->addContextArrayItems( 'tabs', [
    'tab-current'       => [
        'selected'  => true,
        'iconClass' => ($this->witch("target")->hasCraft() && $this->witch("target")->hasInvoke())? "fas fa-hat-wizard"
                            : ($this->witch("target")->hasCraft()? "fas fa-mortar-pestle"
                            : ($this->witch("target")->hasInvoke()? "fas fa-hand-sparkles"
                            : "fas fa-folder")),
        'text'      => "Witch",
    ],
    'tab-craft-part'    => [
        'iconClass' => $this->witch("target")->hasCraft()? "fas fa-mortar-pestle" :"far fa-plus-square",
        'text'      => "Craft",
    ],
    'tab-cauldron-part'    => [
        'iconClass' => $this->witch("target")->hasCauldron()? "fas fa-mortar-pestle": "far fa-plus-square",
        'text'      => "Cauldron",
    ],
]);
?>

<?php $this->include('alerts.php', ['alerts' => $this->wc->user->getAlerts()]); ?>

<div><?php $this->include('view/witch-menu-info.php'); ?></div>
<div><?php $this->include('edit/witch-menu-info.php'); ?></div>

<div class="tabs-target__item selected"  id="tab-current">
    <div class="box-container">
        <div><?php $this->include('view/witch-info.php'); ?></div>
        <div><?php $this->include('edit/witch-info.php', [ 'websitesList' => $websitesList ]); ?></div>
        <div><?php $this->include('view/daughters.php'); ?></div>
        <div><?php $this->include('create/witch.php'); ?></div>
    </div>
</div>

<div class="tabs-target__item"  id="tab-craft-part">
    <div class="box-container">
        <div><?php $this->include('view/craft.php', [ 'structuresList' => $structuresList, 'craftWitches' => $craftWitches ]); ?></div>
        <div><?php $this->include('view/craft-witches.php', [ 'craftWitches' => $craftWitches, 'craftWitchesTargetFirst' => $craftWitchesTargetFirst ]); ?></div>
    </div>
</div>

<div class="tabs-target__item"  id="tab-cauldron-part">
    <div class="box-container">
        <div><?php $this->include('view/cauldron.php'); ?></div>
    </div>
</div>
