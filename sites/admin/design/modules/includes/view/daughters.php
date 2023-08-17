<div class="box view__daughters">
    <h3>
        <i class="fa fa-folder-open"></i>
        Daughters
    </h3>
    
    <?php if( empty($targetWitch->daughters()) ): ?>
        <p><em>No daughters for this witch</em></p>
        
    <?php else: ?>
        <p><em>Witch daughters list in arborescence</em></p>
        <form method="post" 
              id="view-daughters-action" 
              action="<?=$this->wc->website->getUrl('edit?id='.$targetWitch->id) ?>">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Craft</th>
                        <th>Invoke</th>
                        <th>Direct Access</th>
                        <th>Actions</th>
                        <th>Priority</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $targetWitch->daughters() as $daughter ): ?>
                        <tr>
                            <td>
                                <a href="<?=$this->wc->website->getUrl("view?id=".$daughter->id) ?>">
                                    <?=$daughter->name ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?=$this->wc->website->getUrl("view?id=".$daughter->id."#tab-craft-part") ?>"
                                   class="text-center">
                                    <em><?=$daughter->getCraftStructure() ?></em>
                                    <?php if( !$daughter->hasCraft() ): ?>
                                        <em class="hover-hide">no</em>
                                        <i class="far fa-plus-square hover-show"></i>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td>
                                <a  href="<?=$this->wc->website->getUrl("view?id=".$daughter->id."#tab-invoke-part") ?>"
                                    class="text-center">
                                    <em><?=$daughter->invoke ?></em>
                                    <?php if( !$daughter->hasInvoke() ): ?>
                                        <em class="hover-hide">no</em>
                                        <i class="far fa-plus-square hover-show"></i>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td>
                                <?php if( !$daughter->hasInvoke() ): ?>
                                <?php elseif( $daughter->site == $this->wc->website->site ): ?>
                                    <a  target="_blank" href="<?=$daughter->getUrl() ?>" 
                                        class="text-center"
                                        title="<?='['.$daughter->site.'] '.$daughter->invoke ?>">
                                        <i class="fas fa-hand-sparkles"></i>
                                    </a>
                                <?php else: 
                                    $url = $daughter->getUrl( null, $websitesList[ $daughter->site ] ?? null ); ?>
                                    <a target="_blank" href="<?=$url ?>" title="<?=$url ?>">
                                        <em><?='['.$daughter->site.']' ?></em>
                                        <i class="fas fa-hand-sparkles"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="icons-container">
                                <a class="cut-descendants" data-id="<?=$daughter->id ?>" >
                                    <!--i class="fa fa-scissors"></i-->
                                    <i class="fas fa-arrows-alt"></i>
                                </a>
                                
                                <a class="copy-descendants" data-id="<?=$daughter->id ?>">
                                    <i class="fa fa-copy"></i>
                                </a>
                            </td>
                            <td class="text-right">
                                <input  class="priorities-input" 
                                        type="number"
                                        name="priorities[<?=$daughter->id ?>]" 
                                        value="<?=$daughter->priority ?>" />
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <input type="hidden" id="origin-witch" name="origin-witch" value="" />
            <input type="hidden" id="destination-witch" name="destination-witch" value="" />
        </form>
    <?php endif; ?>
    
    <div class="box__actions">
        <button id="move-witch-action" 
                class="trigger-action"
                style="display: none;"
                data-action="move-witch"
                data-target="view-daughters-action">Move witch</button>
        <button id="copy-witch-action" 
                class="trigger-action"
                style="display: none;"
                data-action="copy-witch"
                data-target="view-daughters-action">Copy witch</button>
        <button class="view-daughters__create-witch__toggle">
            <i class="fa fa-plus"></i>
            Add Daughter
        </button>
        <?php if( !empty($targetWitch->daughters()) ): ?>
            <button class="trigger-action" 
                    data-action="edit-priorities"
                    data-target="view-daughters-action">
                <i class="fa fa-sort-numeric-up-alt"></i>
                Edit Priorities
            </button>
        <?php endif; ?>
    </div>
</div>
