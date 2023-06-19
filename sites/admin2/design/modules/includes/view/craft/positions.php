<?php if( $targetWitch->craft() ): ?>
    <div class="box view__craft__positions">
        <h3>Craft Witches</h3>

        <?php if( !$craftWitches ): ?>
            <p><em>No daughters for this witch</em></p>

        <?php else: ?>
            <p><em>Witches list associated with this craft</em></p>
            <table>
                <thead>
                    <tr>
                        <th>Main</th>
                        <th>Name</th>
                        <th>ID</th>
                        <th>Path</th>
                    </tr>
                </thead>
                <tbody>
                    <form method="post" id="view-craft-positions-action">
                        <?php foreach( $craftWitches as $craftPositionWitch ): ?>
                            <tr>
                                <td>
                                    <div class="text-center">
                                    <input type="radio" 
                                           name="main"
                                           value="<?=$craftPositionWitch->id ?>"
                                           <?php if($craftPositionWitch->is_main): ?>
                                                checked
                                           <?php else: ?>
                                                class="trigger-action" 
                                                data-action="switch-main" 
                                                data-target="view-craft-positions-action" 
                                           <?php endif; ?> />
                                    </div>
                                </td>
                                <td>
                                    <a href="<?=$this->wc->website->getUrl("view?id=".$craftPositionWitch->id) ?>">
                                        <?=$craftPositionWitch->name ?>
                                        <em><?=$craftPositionWitch->id == $targetWitch->id? "(this witch)": '' ?></em>
                                    </a>
                                </td>
                                <td>
                                    <div class="text-center"><?=$craftPositionWitch->id ?></div>
                                </td>
                                <td>
                                    <?php foreach( $craftPositionWitch->breadcrumb as $i => $breadcrumbItem ): ?>
                                        <?=($i == 0)? '': '>' ?>
                                        <a href="<?=$breadcrumbItem['href'] ?>" 
                                           target="_blank" 
                                           title="<?=$breadcrumbItem['data'] ?>"><em><?=$breadcrumbItem['name'] ?></em></a>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <input type="hidden" id="new-mother-witch-id" name="new-mother-witch-id" value="" />
                            
                    </form>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="box__actions">
            <button class="trigger-action"
                    id="add-position-action"
                    style="display: none;"
                    data-action="add-position"
                    data-target="view-craft-positions-action">Add position</button>
            <button id="add-craft-position">Add position</button>
        </div>
    </div>
<?php endif; ?>