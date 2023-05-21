<style>
    .login-content {
        border: 1px solid #ccc;
        width: min-content;
        padding: 20px;
        margin: 25px auto 0;
        min-width: 400px;
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 5px 5px 5px #cccccc;    
    }
        .login-content h1 {
            text-align: center;
        }
        .login-content input {
            width: 200px;
            height: 25px;
            font-size: 24px;
        }
</style>
<div class="login-content">
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