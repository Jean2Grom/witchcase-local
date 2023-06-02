<div class="box view__craft">
    <h3>
        <?=!empty($targetWitch->craft())? ucfirst($targetWitch->craft()->structure->type): "Pas de contenu" ?>
    </h3>
    <?php if( empty($targetWitch->craft()) ): ?>
        <form method="post" id="witch-add-new-content">
            <select name="witch-content-structure" id="witch-content-structure">
                <option value="">
                    Pas de contenu
                </option>
                <?php foreach( $structuresList as $structureData ): ?>
                    <option value="<?=$structureData['name']?>">
                        <?=$structureData['name']?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <div class="box__actions">
            <button id="witch__add-content" disabled
                    class="trigger-action"
                    data-action="add-content"
                    data-target="witch-add-new-content">
                Ajouter contenu
            </button>
        </div>

    <?php else: ?>
        <h4>
            <?=$targetWitch->craft()->name ?>
            <span class="content-structure-type">
                [<?=$targetWitch->craft()->structure->name ?>]
            </span>
        </h4>
        <?php foreach( $targetWitch->craft()->attributes as $attribute ): ?>
            <fieldset>
                <legend><?=$attribute->name?> [<?=$attribute->type?>]</legend>
                    <?php $attribute->display() ?>
            </fieldset>
            <div class="clear"></div>
        <?php endforeach; ?>

        <div class="box__actions">
            <?php if( $targetWitch->craft()->structure->type === WC\Craft\Content::TYPE ): ?>
                <button class="trigger-action"
                        data-confirm="Etes vous sur de vouloir archiver le contenu ?"
                        data-action="archive-content"
                        data-target="view-action">
                    Archiver
                </button>
            <?php endif; ?>
            <button class="" 
                    data-href="<?=$editCraftContentHref ?>"
                    id="content__edit">
                Modifier
            </button>
            <button class="trigger-action"
                    data-confirm="Etes vous sur de vouloir supprimer le contenu ?"
                    data-action="delete-content"
                    data-target="view-action">
                Supprimer
            </button>
            <!--button class="trigger-action"
                    data-action="edit-content"
                    data-target="view-action">
                Editer
            </button-->
        </div>
    <?php endif; ?>
</div>
