<header>
    <div class="header__logo">
        <a href="<?=$this->website->getRootUrl()?>">
            <img    id="logo" 
                    src="<?=$this->imageSrc("logo.jpg"); ?>" 
                    alt="Witchcase" 
                    title="Witchcase"/>
        </a>
    </div>
    
    <h1>Admin WitchCase</h1>
    
    <div class="header__user">
        <?php if( $this->wc->user->connexion ): ?>
            <i class="fa fa-user"></i>
            &nbsp;
            <a  href="<?=$this->website->getUrl( "view?id=".$this->wc->witch("user")->id ) ?>">
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
</header>
