<?php
    $this->addCssFile('boxes.css');
    
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
<style>
    .box table td.label {
        min-width: 100px;
        background-color: #eee;
        font-weight: bold;
        text-align: center;
        padding: 5px;        
    }
    .box .box__actions{
        margin: 20px 0px 10px 0;
        text-align: right;
    }
    .box.view__info, 
    .box.edit__info {
        width: 350px;
    }
    .box.view__invoke,
    .box.edit__invoke {
        width: 400px;
    }
</style>

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


<form method="post" id="view-action"></form>

<script>
$(document).ready(function()
{
    $('.edit__info').hide();
    $('button.view-edit-info-toggle').click(function(){
        $('.view__info').toggle();
        $('.edit__info').toggle();
    });
    
    $('.edit__invoke').hide();
    $('button.view-edit-invoke-toggle').click(function(){
        $('.view__invoke').toggle();
        $('.edit__invoke').toggle();
    });
    
});
</script>