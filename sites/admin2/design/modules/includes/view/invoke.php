<div class="box view__invoke">
    <h3>Invoke</h3>
    
    <?php if( $targetWitch->hasInvoke() ): ?>
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
                <td class="label">Status</td>
                <td class="value"><?=$targetWitch->status ?></td>
            </tr>

            <tr>
                <td class="label">Context</td>
                <td class="value"><?=$targetWitch->context ?></td>
            </tr>
        </table>

        <div class="box__actions">
            <button class="view-edit-invoke-toggle">Edit</button>
        </div>
    
    <?php else: ?>
        <p>No module to invoke</p>
        
        <div class="box__actions">
            <button class="view-edit-invoke-toggle">Add</button>
        </div>        
    <?php endif; ?>    
</div>
