<div class="box view__witch-invoke">
    <h3>
        <i class="fa fa-dragon"></i>
        Invoke
    </h3>
    
    <p><em>Invocation allow a witch to be accessed with an URL, and lanch a module to create a response</em></p>
    
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
                <td class="value"><?=$targetWitch->status() ?></td>
            </tr>
            <tr>
                <td class="label">Context</td>
                <td class="value"><?=$targetWitch->context ?></td>
            </tr>
            <tr>
                <td class="label">Direct Access</td>
                <td class="value">
                    <?php if( !$targetWitch->hasInvoke() ): ?>
                    <?php elseif( $targetWitch->site == $this->wc->website->site ): ?>
                        <a  target="_blank" href="<?=$targetWitch->getUrl() ?>" 
                            class="text-center"
                            title="<?='['.$targetWitch->site.'] '.$targetWitch->invoke ?>">
                            <i class="fas fa-hand-sparkles"></i>
                        </a>
                    <?php else: 
                        $url = $targetWitch->getUrl( null, $websitesList[ $targetWitch->site ] ?? null ); ?>
                        <a target="_blank" href="<?=$url ?>" title="<?=$url ?>">
                            <em><?='['.$targetWitch->site.']' ?></em>
                            <i class="fas fa-hand-sparkles"></i>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <div class="box__actions">
            <button class="view-witch-invoke__edit-witch-invoke__toggle">
                <i class="fa fa-pencil"></i>
                Edit
            </button>
        </div>
    
    <?php else: ?>
        <p>No module to invoke</p>
        
        <div class="box__actions">
            <button class="view-witch-invoke__edit-witch-invoke__toggle">
                <i class="fa fa-plus"></i>
                Add
            </button>
        </div>        
    <?php endif; ?>    
</div>
