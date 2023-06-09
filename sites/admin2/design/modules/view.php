<?php
    $this->addCssFile('view.css');
    $this->addCssFile('boxes.css');
    $this->addJsFile('triggers.js');
    $this->addJsFile('view.js');
    
    $this->addContextArrayItems( 'standardContextTabs', [
        'tab-current'       => [
            'selected'  => true,
            'iconClass' => "fas fa-info",
            'text'      => "Witch Info",
        ],
        'tab-craft-part'    => [
            'iconClass' => "fas fa-mortar-pestle",
            'text'      => "Craft",
        ],        
        'tab-invoke-part'   => [
            'iconClass' => "fas fa-hand-sparkles",
            'text'      => "Invoke",
        ],
    ]);
?>
<h1 title="<?=$this->witch->data ?>">
    [<?=$this->witch->name ?>]
    <em><?=$targetWitch->name ?></em>
</h1> 
<p><em><?=$targetWitch->data ?></em></p>

<div class="tabs-target__item selected"  id="tab-current">
    <?php include $this->getIncludeDesignFile('alerts.php'); ?>
    
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
    </div>
</div>

<div class="tabs-target__item"  id="tab-invoke-part">
    <div class="box-container">
        <div><?php include $this->getIncludeDesignFile('view/invoke.php'); ?></div>
        <div><?php include $this->getIncludeDesignFile('edit/invoke.php'); ?></div>
    </div>
</div>
