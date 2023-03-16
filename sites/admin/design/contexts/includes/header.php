<header>
    <div class="header__logo">
        <a href="<?=$this->website->baseUri?>">
            <img    id="logo" 
                    src="<?=$this->getImageFile("logo.jpg"); ?>" 
                    alt="Witchcase" 
                    title="Witchcase"/>
        </a>
    </div>
    
    <?php if( $this->wc->user->connexion ): ?>
        <div class="header__user">
            <div class="header__user__content">
                <i class="fa fa-user"></i>
                &nbsp;
                <a  id="currentuser-a"  
                    href="<?=$this->website->baseUri.'/view?id='.$this->website->witches["user"]->id ?>">
                    <?=$this->wc->user->name ?>
                </a>
                &nbsp;
                <a href="<?=$this->website->baseUri."/login"?>">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
    
    <h1>Admin WitchCase</h1>
</header>
