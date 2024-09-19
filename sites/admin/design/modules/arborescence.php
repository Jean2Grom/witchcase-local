<?php /** @var WC\Module $this */

$this->addJsLibFile('jquery-3.6.0.min.js');
$this->addJsFile('fontawesome.js');
$this->addCssFile('arborescence-menu.css');
$this->addJsFile('arborescence-menu.js');

$key = "arborescence_".md5(microtime().rand());
?>
<div id="<?=$key ?>" class="arborescence-menu-container module"></div>

<script type="text/javascript">
    if( arborescencesInputs === undefined ){
        var arborescencesInputs = {};
    }
    
    arborescencesInputs[ "<?=$key ?>" ] = {
        "treeData": <?=json_encode($tree)?>,
        "currentId": <?=$currentId ?? 0?>,
        "currentSite": "<?=$this->wc->website->name?>",
        "breadcrumb": <?=json_encode($breadcrumb)?>,
        "draggable": <?=$draggble ?? null ? "true": "false"?>
    };
</script>


