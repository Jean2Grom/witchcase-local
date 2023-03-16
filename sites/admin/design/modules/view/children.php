<?php

?>

<div id="content-view-children">
    <? if( $goUp ) { ?>
        <div id="content-view-children-result">
            <a  title="Monter d'un niveau" 
                href="<?=$goUp['href']?>">
                <img    width="14"
                        title="Monter d'un niveau" 
                        alt="Monter d'un niveau" 
                        src="<?=$goUp['img']?>" />
                Monter d'un niveau
            </a>
        </div>
    <? } ?>

    <? if( $displayChildren ) { ?>
        <!-- Children START -->
        <form action="" method="post" name="children">
            <? if( count($children) ) { ?>
                <h2 class="context-title">
                    Sous-éléments (<?=$childrenCount?>)
                </h2>
                <div id="navHeader">
                    Afficher
                    <? foreach( $limits as $limit => $href ) { ?>
                        <? if($href) { ?>
                            <a href="<?=$href?>">
                                <?=$limit?>
                            </a>
                        <? } else { ?>
                            <span>
                                <?=$limit?>
                            </span>
                        <? } ?>
                     <? } ?>
                    éléments
                </div>
                <? if( $archiveHref ) { ?>
                    <a id="navHeader-a" 
                        href="<?=$archiveHref["href"]?>">
                        <?=$archiveHref["name"]?>
                    </a>
                <? } ?>
                <div class="box-content">
                    <table id="box-content-table">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <? foreach( $childrenHeader as $header => $href ) { ?>
                                    <th>
                                        <? if( $href ) { ?>
                                            <a href="<?=$href?>">
                                                <?=$header?>
                                            </a>
                                        <? } else { ?>
                                            <?=$header?>
                                        <? } ?>
                                    </th>
                                <? } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach( $children as $child ) { ?>
                                <tr>
                                    <td>
                                        <input  type="checkbox" 
                                                value="<?=$child['id']?>" 
                                                name="childrenID[]" />
                                        <input  type="hidden"
                                                name="childrenNames[<?=$child['id']?>]"
                                                value="<?=$child['name']?>" />
                                    </td>
                                    <td>
                                        <? if($child['href']) { ?>
                                            <a href="<?=$child['href']?>">
                                                <?=$child['name']?>
                                            </a>
                                        <? } else { ?>
                                            <?=$child['name']?>
                                        <? } ?>
                                    </td>
                                    <td>
                                        <?=$child['module']?>
                                    </td>
                                    <td>
                                        <?=$child['state']?>
                                    </td>
                                    <td>
                                        <?=$child['type']?>
                                    </td>
                                    <td>
                                        <?=$child['status']?>
                                    </td>
                                    <td>
                                        <input id="box-content-table-priority"  type="number" 
                                                value="<?=$child['priority']?>" 
                                                name="priorities[<?=$child['location_id']?>]" />
                                    </td>
                                </tr>
                            <? } ?>
                        </tbody>
                    </table>

                    <div class="pagination">
                        <ul>
                            <? foreach( $pages as $key => $pageLinkData ) { ?>
                                <? if( !is_int($key) ) { ?>
                                    <li id="first-li-pagination">
                                        <a href="<?=$pageLinkData['href']?>">
                                            <?=$key?>
                                        </a>
                                    </li>
                                <? } elseif( !$pageLinkData['href'] ) { ?>
                                    <li id="second-li-pagination">
                                        &nbsp;<?=$pageLinkData['name']?>
                                    </li>
                                <? } else { ?>
                                    <li id="second-li-pagination">
                                        <a href="<?=$pageLinkData['href']?>">
                                            &nbsp;<?=$pageLinkData['name']?>
                                        </a>
                                    </li>
                                <? } ?>
                            <? } ?>
                        </ul>
                    </div>

                </div>
            <? } else { ?>
                <h2 class="context-title">
                    Aucun sous-éléments
                </h2>
            <? } ?>

            <div id="action-controls">
                <a href="<?=$creationHref?>">
                    <input  type="button" 
                            title="Créer un nouvel élément sous celui-ci." 
                            value="Créer élément" 
                            name="createButton" 
                            class="button" />
                </a>
                <? if( count($children) ) { ?>
                    <input  type="submit" 
                            title="Supprimer un sous-élément avec sa sous-arborescence." 
                            value="Supprimer selection" 
                            name="deleteChildren" 
                            class="button" />
                    <input  type="submit" 
                            title="Créer un nouvel élément sous celui-ci." 
                            value="Changer priorités" 
                            name="changePriorities" 
                            class="button" />
                <? } ?>
            </div>
        </form>
        <!-- Children END -->
    <? } ?>

</div>
