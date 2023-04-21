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
            .structures-content__list table {
                border-collapse: collapse;
                margin-bottom: 10px;
            }
            .structures-content__list table th {
                min-width: 100px;
                background-color: #ddd;
                padding: 5px 10px;
            }
            .structures-content__list table tbody tr:hover {
                background-color: #eee;
            }
            .structures-content__list table td {
                padding: 4px 10px;
                text-align: center;
            }
            .structures-content__list table td:first-child {
                text-align: left;
            }
            .structures-content__list table tr td:last-child {
                text-align: right;
            }
            .structures-content__list table input {
                width: 60px;
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
                        <th>Contents</th>
                        <th>Création</th>
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
                                <?=$structure['count']['content']?>
                            </td>
                            <td>
                                <?=$structure['creation']->format( 'H:i:s d/m/Y' )?>
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