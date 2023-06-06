<?php 
    $this->addJsFile('triggers.js');
?>
<div class="box view__position">
    <h3>
        Witches matriarchy
    </h3>
    <p><em>Position in arborescence: mother and daughters</em></p>
    <table>
        <thead>
            <tr>
                <?php foreach( $subTree['headers'] as $header ): ?>
                    <th>
                        <?=$header?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if( $upLink ): ?>
                <tr>
                    <td>
                        <a href="<?=$upLink ?>" 
                           title="<?=$targetWitch->mother()->name ?>">
                            <i class="fas fa-reply rotate-90"></i>
                            <?=$targetWitch->mother()->name ?>
                        </a>
                    </td>
                    <td>
                        <?=$targetWitch->mother()->site ?>
                    </td>
                    <td>
                        <?php if( !empty($targetWitch->mother()->invoke) && $targetWitch->mother()->hasCraft() ): ?>
                            Module & Contenu
                        <?php elseif( !empty($targetWitch->mother()->invoke) ): ?>
                            Module
                        <?php elseif( $targetWitch->mother()->hasCraft() ): ?>
                            Contenu
                        <?php else: ?>
                            Répertoire
                        <?php endif; ?>
                    </td>
                    <td class="text-right"></td>
                </tr>
            <?php endif; ?>
            
            <form method="post" id="view-position-action">
                <?php foreach( $subTree['data'] as $daughter ): ?>
                    <tr>
                        <td>
                            <a href="<?=$this->wc->website->baseUri."/view?id=".$daughter->id ?>">
                                <?=$daughter->name ?>
                            </a>
                        </td>
                        <td>
                            <?=$daughter->site ?>
                        </td>
                        <td>
                            <?php if( !empty($daughter->invoke) && $daughter->hasCraft() ): ?>
                                Module & Contenu
                            <?php elseif( !empty($daughter->invoke) ): ?>
                                Module
                            <?php elseif( $daughter->hasCraft() ): ?>
                                Contenu
                            <?php else: ?>
                                Répertoire
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <input  class="priorities-input" 
                                    type="number"
                                    name="priorities[<?=$daughter->id ?>]" 
                                    value="<?=$daughter->priority ?>" />
                        </td>
                    </tr>
                <?php endforeach; ?>
            </form>
        </tbody>
    </table>

    <div class="box__actions">
        <button class="trigger-href" 
                data-href="<?=$createElementHref?>">Add Daughter</button>
        <button class="trigger-action" 
                data-action="edit-priorities"
                data-target="view-position-action">Edit Priorities</button>
    </div>
</div>
