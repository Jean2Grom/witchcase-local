<?php

?>

<h1>Votre module <?=$name?> a bien été créé</h1>
<br/>

<fieldset>
    <p>
        Votre fichier contrôleur : <?=$moduleControllerFile?>
    </p>
    <p>
        Votre fichier de visualisation : <?=$moduleDesignFile?>
    </p>
</fieldset>

<p>
    <a href="<?=$returnHref?>">
        <input  type="button"
                value="OK" />
    </a>
</p>