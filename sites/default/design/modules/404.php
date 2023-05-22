<?php
    $this->addCssFile('basic.css');
?>
<div class="content">
    <h1>404</h1>
    <p>Cette page n'existe pas ou plus</p>
    <p>
        <a href="<?=$this->wc->website->getRootUrl() ?>">
            Retour sur la page d'accueil
        </a>
    </p>
</div>
