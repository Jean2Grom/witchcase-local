<?php
    $this->addCssFile('view.css');
    $this->addCssFile('boxes.css');
    $this->addJsFile('triggers.js');
    $this->addJsFile('view.js');
    
    $this->addContextArrayItems( 'tabs', [
        'tab-current'       => [
            'selected'  => true,
            //'iconClass' => "fas fa-sitemap",
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
        'tab-invoke-part'   => [
            'iconClass' => !$targetWitch->hasInvoke()? "far fa-plus-square": ($targetWitch->hasCraft()? "fas fa-hand-sparkles": ""),
            'text'      => "Invoke",
        ],
    ]);
?>
<h2 title="<?=$this->witch->data ?>"><?=$targetWitch->name ?></h2> 
<p><em><?=$targetWitch->data ?></em></p>

<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<div class="tabs-target__item selected"  id="tab-current">
    <div class="box-container">
        <div><?php include $this->getIncludeDesignFile('view/info.php'); ?></div>
        <div><?php include $this->getIncludeDesignFile('edit/info.php'); ?></div>
        <div><?php include $this->getIncludeDesignFile('view/position.php'); ?></div>
        <div><?php include $this->getIncludeDesignFile('create/info.php'); ?></div>
    </div>
</div>

<div class="tabs-target__item"  id="tab-craft-part">
    <div class="box-container">
        <div><?php include $this->getIncludeDesignFile('view/craft.php'); ?></div>
        <div><?php include $this->getIncludeDesignFile('view/craft/positions.php'); ?></div>
    </div>
</div>

<div class="tabs-target__item"  id="tab-invoke-part">
    <div class="box-container">
        <div><?php include $this->getIncludeDesignFile('view/invoke.php'); ?></div>
        <div><?php include $this->getIncludeDesignFile('edit/invoke.php'); ?></div>
    </div>
</div>
