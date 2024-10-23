<?php /** @var WC\Module $this */

$this->addCssFile('boxes.css');
$this->addJsFile('triggers.js');

?>
<div class="box view-content">
    <h1><?=$this->witch()->name?></h1>
    <p><?=$this->witch()->data?></p>

    <?php $this->include('alerts.php', ['alerts' => $this->wc->user->getAlerts()]); ?>

    <div id="navHeader">
        <h2><?=count( $recipeArray )?> Recipe structures</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Implementations</th>
                <th>Usages in Witches</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $recipeArray as $recipe => $data ): ?>
                <tr>
                    <td>
                        <a href="<?=$this->wc->website->getUrl( 'recipe/view', ['recipe' => $recipe] )?>">
                            <?=$data['name']?>
                        </a>
                    </td>
                    <td><span class="text-center"><?=$data['cauldron']?></span></td>
                    <td><span class="text-center"><?=$data['witches']?></span></td>
                    <td>
                        <div class="text-center">
                            <a href="<?=$this->wc->website->getUrl( 'recipe/view', ['recipe' => $recipe] )?>">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="<?=$this->wc->website->getUrl( 'recipe/edit', ['recipe' => $recipe] )?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="box__actions">
        <button class="trigger-href" data-href="<?=$this->wc->website->getUrl('recipe/create')?>">
            <i class="fa fa-plus" aria-hidden="true"></i>
            Add New Recipe
        </button>
    </div>
</div>