<?php 
    $this->addCssFile('basic.css');
?>
<div class="content">
    <h1>Identification</h1>
    
    <?php include $this->getIncludeDesignFile('alerts.php'); ?>
    
    <?php if( $this->isRedirection ): ?>
        <p>Vous devez vous identifier pour accéder à cette page.</p>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="login" value="login" />
        
        <label for="username">Nom d'utilisateur ou email</label>
        <input type="text" name="username" id="username"/>
        
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" />
        
        <button class="">
            Se connecter
        </button>
    </form>
</div>