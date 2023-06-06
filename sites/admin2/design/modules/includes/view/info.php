<?php 
    $this->addJsFile('triggers.js');
?>
<div class="box view__info">
    <h3><?=$targetWitch->name ?></h3>
    
    <table>
        <tr>
            <td class="label">Name</td>
            <td class="value"><?=$targetWitch->name ?></td>
        </tr>
        <tr>
            <td class="label">Description</td>
            <td class="value"><?=$targetWitch->data ?></td>
        </tr>
        <tr>
            <td class="label">ID</td>
            <td class="value"><?=$targetWitch->id ?></td>
        </tr>
        <tr>
            <td class="label">Craft</td>
            <td class="value">
                <a class="tabs__item__triggering" href="#tab-craft-part">
                    <?=!$targetWitch->hasCraft()? 'no': $targetWitch->craft_table.": ".$targetWitch->craft_fk ?>
                </a>
            </td>
        </tr>
        <tr>
            <td class="label">Invoke</td>
            <td class="value">
                <a class="tabs__item__triggering" href="#tab-invoke-part">
                    <?=!$targetWitch->hasInvoke()? 'no': $targetWitch->invoke ?>
                </a>
            </td>
        </tr>
    </table>

    <div class="box__actions">
        <?php if( $targetWitch->mother() ): ?>
            <button class="trigger-action" 
                    data-confirm="Warning ! You are about to remove the witch whith all descendancy"
                    data-target="view-info-action"
                    data-action="delete-witch">Delete</button>
        <?php endif; ?>
        <button class="view-edit-info-toggle">Edit</button>
    </div>
</div>

<form method="post" id="view-info-action"></form>

<script>
$(document).ready(function()
{
    $('.tabs__item__triggering').click(function(){
        triggerTabItem( $(this).attr('href') );
        return false;
    });
});
</script> 