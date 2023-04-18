<?php
    $this->addCssFile('structures.css');
?>
<style>
    .rotate-90 {
        transform: rotate(90deg);
    }
        
    .structures-content__list {
        float: left;
        border: 1px solid #ccc;
        border-radius: 10px;
        margin: 15px 15px 5px 0;
        padding: 10px;
        box-shadow: 5px 5px 5px #ccc;
    }
        .structures-content__list h2 {
            font-size: 1.1em;
            margin-top: 5px;
        }
            .structures-content__list table th {
                min-width: 100px;
                background-color: #eee;
            }
            .structures-content__list table td {
                padding: 1px 10px;
            }
            .structures-content__list table input {
                width: 60px;
            }
        .structures-content__list__actions {
            margin: 20px 0px 10px 0;
            text-align: right;
        }
    #witch__add-content {
        margin-top: 15px;
    }
</style>
<div class="view-content">
    <h1>Structures des données</h1>

    <div class="errorMessages">
        <?php foreach( $messages as $message ): ?>
            <p><?=$message?></p>
        <?php endforeach; ?>
    </div>

    <p>
        Les données sont stockées sous la forme de structures qui sont éditables ici.
    </p>

    <div class="structures-content__list">
        <form method="POST" name="structures">
            <div id="navHeader">
            <h2><?=$count?> Structures</h2>
            </div>
            <?php /* if( $archiveHref ): ?>
                <a id="structures-navHeader-a" 
                    href="<?=$archiveHref["href"]?>">
                    <?=$archiveHref["name"]?>
                </a>
            <?php endif; */?>

            <table id="structures-navHeader-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Création</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $structures as $structure ): ?>
                        <tr>
                            <td>
                                <a href="<?=$structure['viewHref']?>">
                                    <?=$structure['name']?>
                                </a>
                            </td>
                            <?php /* if($archives): ?>
                                <td align="center">
                                    <?=$structure['isArchive']?>
                                </td>
                            <?php endif; */ ?>
                            <td>
                                <?=$structure['creation']->format( 'H:i:s d/m/Y' )?>
                            </td>
                            <td>
                                <a href="<?=$structure['viewHref']?>">
                                    Voir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <input  type="submit"
                    name="createStructure"
                    value="Créer Structure" />
        </form>
    </div>
    <div class="clear"></div>
</div>