<div class="box view__info">
    <h3>Info</h3>
    <p><em>Wich global information</em></p>
    
    <table>
        <tr>
            <td class="label">Name</td>
            <td class="value"><?=$targetWitch->name ?></td>
        </tr>
        <tr>
            <td class="label">Description</td>
            <td class="value"><em><?=$targetWitch->data ?></em></td>
        </tr>
        <tr>
            <td class="label">Craft</td>
            <td class="value">
                <a class="tabs__item__triggering" href="#tab-craft-part">
                    <em><?=$targetWitch->getCraftStructure() ?></em>
                    <?php if( !$targetWitch->hasCraft() ): ?>
                        <em class="hover-hide">no</em>
                        <i class="far fa-plus-square hover-show"></i>
                    <?php endif; ?>
                </a>
            </td>
        </tr>
        <tr>
            <td class="label">Invoke</td>
            <td class="value">
                <a class="tabs__item__triggering" href="#tab-invoke-part">
                    <em><?=$targetWitch->invoke ?></em>
                    <?php if( !$targetWitch->hasInvoke() ): ?>
                        <em class="hover-hide">no</em>
                        <i class="far fa-plus-square hover-show"></i>
                    <?php endif; ?>
                </a>
            </td>
        </tr>
        <tr>
            <td class="label">ID</td>
            <td class="value"><?=$targetWitch->id ?></td>
        </tr>
        <?php if( $targetWitch->mother() ): ?>
            <tr>
                <td class="label">Mother</td>
                <td class="value">
                    <a href="<?=$this->wc->website->getUrl("view?id=".$targetWitch->mother()->id) ?>">
                        <i class="fas fa-reply rotate-90"></i>
                        <?=$targetWitch->mother() ?>
                    </a>
                </td>
            </tr>
        <?php endif; ?>
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

