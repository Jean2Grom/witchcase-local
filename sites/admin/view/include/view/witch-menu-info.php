<?php /** @var WC\Module $this */ ?>

<div class="view__witch-menu-info">
    <h2 title="<?=$this->witch->data ?>">
        <?=$this->witch("target")->name ?>
        <button class="view-edit-menu-info-toggle">
            <i class="fa fa-pencil"></i>
        </button>        
    </h2>
    
    <p><em><?=$this->witch("target")->data ?></em></p>
</div>
