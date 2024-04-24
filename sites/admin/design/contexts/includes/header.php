<?php /** @var WC\Context $this */ ?>

<?php if( count($this->witch('menu')?->daughters() ?? []) > 0 ): ?>
    <a class="side-nav-toggler">
        <i class="fa fa-bars"></i>
    </a>
<?php endif; ?>

<div class="logo">
    <a href="<?=$this->website->getRootUrl()?>">
        <img    src="<?=$this->imageSrc("logo.jpg"); ?>" 
                alt="Witchcase" 
                title="Witchcase"/>
    </a>
</div>

<div class="header-user">
    <?php if( $this->wc->user->connexion ): ?>
        <i class="fa fa-user"></i>
        &nbsp;
        <a  href="<?=$this->website->getUrl( "view?id=".$this->witch("user")->id ) ?>">
            <?=$this->wc->user->name ?>
        </a>
        &nbsp;
        <a href="<?=$this->website->getUrl("login") ?>">
            <i class="fa fa-times"></i>
        </a>
    <?php else: ?>
        &nbsp;
    <?php endif; ?>    
</div>
