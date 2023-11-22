<?php /** @var WC\Module $this */ ?>

<h1>Login</h1>

<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<?php if( $this->isRedirection ): ?>
    <p><em>Login requested</em></p>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="action" value="login" />

    <label for="username">Username or email</label>
    <input type="text" name="username" id="username"/>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" />

    <button class="">
        Sign in
    </button>
</form>
