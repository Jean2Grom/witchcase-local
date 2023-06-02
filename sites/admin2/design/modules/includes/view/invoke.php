<div class="box view__invoke">
    <h3>
        Invoke
    </h3>

    <table>
        <tr>
            <td class="label">Module</td>
            <td class="value"><?=$targetWitch->invoke ?></td>
        </tr>
        <tr>
            <td class="label">Site</td>
            <td class="value"><?=$targetWitch->site ?></td>
        </tr>
        <tr>
            <td class="label">URL</td>
            <td class="value"><?=$targetWitch->url ?></td>
        </tr>
        <tr>
            <td class="label">Statut</td>
            <td class="value"><?=$targetWitch->status ?></td>
        </tr>

        <tr>
            <td class="label">Context</td>
            <td class="value"><?=$targetWitch->context ?></td>
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
        <button class="" 
                data-href="<?=$editCraftWitchHref ?>"
                id="witch__edit">
            Modifier
        </button>
    </div>
</div>
