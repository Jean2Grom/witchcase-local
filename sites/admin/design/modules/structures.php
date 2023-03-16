<?php

$this->addCssFile('structures.css');
?>

<h1>Structures des données</h1>

<div class="errorMessages">
    <?php foreach( $messages as $message ): ?>
        <p><?=$message?></p>
    <?php endforeach; ?>
</div>

<p>
    Les données sont stockées sous la forme de structures qui sont éditables ici.
</p>

<div class="respiration">
    <form method="POST" name="structures">
        <div id="navHeader">
        <h2><?=$count?> Structures</h2>
        </div>
        <?php if( $archiveHref ): ?>
            <a id="structures-navHeader-a" 
                href="<?=$archiveHref["href"]?>">
                <?=$archiveHref["name"]?>
            </a>
        <?php endif; ?>
        
        <table id="structures-navHeader-table">
            <thead>
                <tr>
                    <th>&nbsp;

                    </th>
                    <?php foreach( $headers as $header => $href ): ?>
                        <th>
                            <?php if( $href ): ?>
                                    <?=$header?>
                                <a href="<?=$href?>">
                                    <div class="triangle"></div>
                                </a>
                            <?php else: ?>
                                <?=$header?>
                            <?php endif; ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $structures as $structure ): ?>
                    <tr>
                        <td>
                                <input  type="checkbox" 
                                        name="structures[]" 
                                        <?php if( !$structure['modifyHref'] ): ?>
                                            disabled="disabled"
                                        <?php endif; ?>
                                        value="<?=$structure['name']?>" />
                        </td>
                        <td>
                            <a href="<?=$structure['viewHref']?>">
                                <?=$structure['name']?>
                            </a>
                        </td>
                        <?php  if($archives): ?>
                            <td align="center">
                                <?=$structure['isArchive']?>
                            </td>
                        <?php endif; ?>
                        <td align="center">
                            <?=$structure['draftCount']?>
                        </td>
                        <td align="center">
                            <?=$structure['contentCount']?>
                        </td>
                        <td align="center">
                            <?=$structure['archiveCount']?>
                        </td>
                        <td>
                            <?=$structure['creation']->frenchFormat(true)?>
                        </td>
                        <?php if( $structure['modifyHref'] ):?>
                            <td>
                                <a href="<?=$structure['modifyHref']?>">
                                    Modifier
                                </a>
                            </td>
                        <?php else: ?>
                            <td>
                                <a href="<?=$structure['viewHref']?>">
                                    Voir
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <input  type="submit"
                name="createStructure"
                value="Créer Structure" />
        <input  type="submit"
                name="deleteStructures"
                value="Supprimer Structures" />
    </form>
</div>