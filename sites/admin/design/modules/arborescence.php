<div class="arborescence-menu-container module"></div>

<script type="text/javascript">
    var treeData    = <?=json_encode($tree)?>;
    var currentId   = <?=$currentId?>;
    var currentSite = "<?=$this->wc->website->name?>";
    var breadcrumb  = <?=json_encode($breadcrumb)?>;
    var initPath    = false;
</script>


