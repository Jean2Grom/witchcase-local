<?php 
    $this->addCssFile('basic.css');
?>
<div class="content">
    <h1>Identification</h1>
    
    <?php include $this->getIncludeDesignFile('alerts.php'); ?>
    
    <p>
        Vous devez vous identifier pour accéder à cette page.
    </p>

    <p>
        <form method="POST">
            <input type="hidden" name="login" value="login" />

            <p>
                <h4>Nom d'utilisateur ou email</h4>
                <input type="text" name="username" />
            </p>
            <p>
                <h4>Mot de passe</h4>
                <input type="password" name="password" />
            </p>
            <button class="">
                Se connecter
            </button>
        </form>
    </p>
</div>