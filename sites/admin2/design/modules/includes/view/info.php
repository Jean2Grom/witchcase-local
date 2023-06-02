<div class="box view__info">
    <h3>
        <?=$targetWitch->name ?>
    </h3>

    <table>
        <tr>
            <td class="label">Nom</td>
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
    </table>

    <div class="box__actions">
        <?php if( $upLink ): ?>
            <button class="" 
                    data-confirm="Attention ! Vous allez également supprimer la sous-arborescence."
                    id="witch__delete">
                Supprimer
            </button>
        <?php endif; ?>
        <button class="view-edit-info-toggle">Edit</button>
    </div>
</div>