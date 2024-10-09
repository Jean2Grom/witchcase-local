<?php /** @var WC\Module $this */ ?>

<div class="box view__witch-info">
    <h3 class="box-info">
        <img src="<?=$this->image('favicon.png') ?>" />
        Witch Information
    </h3>
    <p><em>Wich inner information</em></p>
    
    <table class="vertical">
        <tr>
            <td class="label">Witch ID</td>
            <td class="value"><?=$this->witch("target")->id ?></td>
        </tr>        
        <tr>
            <td class="label">Site</td>
            <td class="value">
                <?php if( $this->witch("target")->site ): ?>
                    <strong><?=$this->witch("target")->site ?></strong>
                <?php else: ?>
                    <em>no</em>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td class="value"><?=$this->witch("target")->status() ?? "" ?></td>
        </tr>
        <tr>
            <td class="label">Craft</td>
            <td class="value">
                <a class="tabs__item__triggering" href="#tab-craft-part">
                    <em><?=$this->witch("target")->getCraftStructure() ?></em>
                    <?php if( !$this->witch("target")->hasCraft() ): ?>
                        <em class="hover-hide">no</em>
                        <i class="far fa-plus-square hover-show"></i>
                    <?php endif; ?>
                </a>
            </td>
        </tr>
        <?php if( $this->witch("target")->hasInvoke() ): ?>
            <tr>
                <td class="label">URL</td>
                <td class="value"><?=!is_null($this->witch("target")->url)? '/'.$this->witch("target")->url: "No"?></td>
            </tr>
            <tr>
                <td class="label">Invoke</td>
                <td class="value">
                    <?php if( $this->witch("target")->hasInvoke() ): ?>
                        <strong><?=$this->witch("target")->invoke ?></strong>
                    <?php else: ?>
                        <em>no</em>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="label">Direct Access</td>
                <td class="value">
                    <a  target="_blank" 
                        href="<?=$this->witch("target")->url(null, (new WC\Website($this->wc, $this->witch("target")->site) )) ?? "" ?>">
                        <i class="fas fa-hand-sparkles" aria-hidden="true"></i>
                    </a>
                </td>
            </tr>
        <?php endif; ?>
    </table>
    
    <div class="box__actions">
        <?php if( $this->witch("target")->mother() ): ?>
            <button class="trigger-action" 
                    data-confirm="Warning ! You are about to remove the witch whith all descendancy"
                    data-target="view-info-action"
                    data-action="delete-witch">
                <i class="fa fa-trash"></i>
                Delete
            </button>
        <?php endif; ?>
        <button class="view-edit-info-toggle">
            <i class="fa fa-pencil"></i>
            Edit
        </button>
    </div>
</div>

<form method="post" 
      action="<?=$this->wc->website->getUrl('edit?id='.$this->witch("target")->id) ?>"
      id="view-info-action"></form>

