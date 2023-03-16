<style>
    .not-found-content {
        border: 1px solid #ccc;
        width: min-content;
        padding: 20px;
        margin: 25px auto 0;
        min-width: 400px;
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 5px 5px 5px #cccccc;    
    }
        .not-found-content h1,
        .not-found-content p {
            text-align: center;
        }
</style>
<div class="not-found-content">
    <h1>404</h1>
    <p>Cette page n'existe pas ou plus</p>
    <p>
        <a href="<?=$this->wc->website->baseUri?>">
            Retour sur la page d'accueil
        </a>
    </p>
</div>
