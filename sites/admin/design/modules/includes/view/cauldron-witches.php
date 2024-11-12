<?php /** @var WC\Module $this */

$this->wc->debug(
    $this->witch("target")->cauldron()->witches, 
    "Cauldron's witches",
    2
);

if( $this->witch("target")->cauldron() ): ?>
    <div class="box">
        <h3>
            <i class="fa fa-project-diagram"></i>
            Cauldron Witches
        </h3>

        <?php if( count($this->witch("target")->cauldron()->witches) === 1 ): ?>
            <p><em>No other witch</em></p>

        <?php else: ?>
            <p><em>Cauldron's associated witches list</em></p>
            <table>
                <thead>
                    <tr>
                        <th>Main</th>
                        <th>ID</th>
                        <th>Path</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <form method="post" 
                          action="<?=$this->wc->website->getUrl('view', [ 'id' => $this->witch("target")->id ]) ?>"
                          id="view-cauldron-witches-action">
                        <?php foreach( $this->witch("target")->cauldron()->witches as $key => $witch ): ?>
                            <tr>
                                <td>
                                    <div class="text-center">
                                        <input type="radio" 
                                               name="main"
                                               value="<?=$witch->id ?>"
                                               <?php if( array_key_first($this->witch("target")->cauldron()->witches) === $key ): ?>
                                                    checked
                                               <?php else: ?>
                                                    class="trigger-action" 
                                                    data-action="switch-craft-main-witch" 
                                                    data-target="view-craft-witches-action" 
                                               <?php endif; ?> />
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center"><?=$witch->id ?></div>
                                </td>
                                <td>
                                    <?=$witch->cauldronPriority ?>
                                </td>
                                <td>
                                    <?php if( $witch->id === $this->witch("target")->id ): ?>
                                        <span class="highlighted">
                                            <?=$witch->name ?>
                                            <em>(this witch)</em>
                                        </span>
                                    <?php else: ?>
                                        <a href="<?=$this->wc->website->getUrl( "view", ['id'=> $witch->id] ) ?>#tab-cauldron-part">
                                            <?=$witch->name ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <input type="hidden" id="cauldron-new-witch-id" name="cauldron-new-witch-id" value="" />
                            
                    </form>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="box__actions">
            <button class="trigger-action"
                    id="cauldron-add-new-witch-action"
                    style="display: none;"
                    data-action="cauldron-add-new-witch"
                    data-target="view-cauldron-witches-action">Add cauldron new witch action</button>
            <button class="trigger-action"
                    id="cauldron-add-witch-action"
                    style="display: none;"
                    data-action="cauldron-add-witch"
                    data-target="view-cauldron-witches-action">Add cauldron witch action</button>

            <button id="add-cauldron-new-witch">
                <i class="fa fa-plus"></i>
                Create new cauldron witch
            </button>
            <button id="add-cauldron-witch">
                <i class="fa fa-mortar-pestle"></i>
                Add to witch
            </button>
        </div>
    </div>
<?php endif; ?>