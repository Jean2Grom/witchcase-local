<?php /** @var WC\Module $this */

$this->addCssFile('boxes.css');
$this->addJsFile('triggers.js');

?>
<div class="box view-content">
    <h1><?=$this->witch()->name?></h1>
    <p><?=$this->witch()->data?></p>

    <?php include $this->getIncludeDesignFile('alerts.php'); ?>

    <div class="structures-content__list">
        <form method="POST" name="structures">
            <div id="navHeader">
                <h2><?=count( $structureArray )?> Structures</h2>
            </div>

            <table id="structures-navHeader-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Implementations</th>
                        <th>Usages in Witches</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $structureArray as $structure => $data ): ?>
                        <tr>
                            <td>
                                <a href="<?=$this->wc->website->getUrl( 'structure/view', ['structure' => $structure] )?>">
                                    <?=$data['name']?>
                                </a>
                            </td>
                            <td><span class="text-center"><?=$data['cauldron']?></span></td>
                            <td><span class="text-center"><?=$data['witches']?></span></td>
                            <td>
                                <div class="text-center">
                                    <a href="<?=$this->wc->website->getUrl( 'structure/view', ['structure' => $structure] )?>">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="<?=$structure?>">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="box__actions">
                <button class="trigger-href" data-href="/admin/view?id=24">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Add Daughter
                </button>
            </div>
        </form>
    </div>
</div>