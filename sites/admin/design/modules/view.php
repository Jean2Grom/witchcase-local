<?php /** @var WC\Module $this */

$this->addCssFile('view.css');
$this->addCssFile('boxes.css');
$this->addJsFile('triggers.js');
$this->addJsFile('view.js');

$this->addContextArrayItems( 'tabs', [
    'tab-current'       => [
        'selected'  => true,
        'iconClass' => ($targetWitch->hasCraft() && $targetWitch->hasInvoke())? "fas fa-hat-wizard"
                            : ($targetWitch->hasCraft()? "fas fa-mortar-pestle"
                            : ($targetWitch->hasInvoke()? "fas fa-hand-sparkles"
                            : "fas fa-folder")),
        'text'      => "Witch",
    ],
    'tab-craft-part'    => [
        'iconClass' => !$targetWitch->hasCraft()? "far fa-plus-square": ($targetWitch->hasInvoke()? "fas fa-mortar-pestle": ""),
        'text'      => "Craft",
    ],
    'tab-cauldron-part'    => [
        'iconClass' => !$targetWitch->hasCauldron()? "far fa-plus-square": ($targetWitch->hasInvoke()? "fas fa-mortar-pestle": ""),
        'text'      => "Cauldron",
    ],
]);
?>

<?php $this->include('alerts.php', ['alerts' => $this->wc->user->getAlerts()]); ?>

<?php include $this->getIncludeDesignFile('view/witch-menu-info.php'); ?>
<?php include $this->getIncludeDesignFile('edit/witch-menu-info.php'); ?>


<div class="tabs-target__item selected"  id="tab-current">
    <div class="box-container">
        <div><?php include $this->getIncludeDesignFile('view/witch-info.php'); ?></div>
        <div><?php include $this->getIncludeDesignFile('edit/witch-info.php'); ?></div>
        <div><?php include $this->getIncludeDesignFile('view/daughters.php'); ?></div>
        <div><?php include $this->getIncludeDesignFile('create/witch.php'); ?></div>
    </div>
</div>

<div class="tabs-target__item"  id="tab-craft-part">
    <div class="box-container">
        <div><?php include $this->getIncludeDesignFile('view/craft.php'); ?></div>
        <div><?php include $this->getIncludeDesignFile('view/craft-witches.php'); ?></div>
    </div>
</div>

<div class="tabs-target__item"  id="tab-cauldron-part">
    <div class="box-container">
        <div><?php $this->include('view/cauldron.php', [
            'cauldron' => $this->witch("target")->cauldron()
        ]); ?></div>
    </div>
</div>
