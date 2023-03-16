<?php

?>

<h1>
    Création de votre nouvel emplacement
</h1>

<div class="errorMessages" style="color: #ff9900; font-style: italic">
    <? foreach( $messages as $message ) { ?>
    <p><?=$message?></p>
    <? } ?>
</div>

<p>
    Il est nécessaire de déterminer l'emplacement, puis le nommer et le décrire.
</p>

<form name="chooseType" id="chooseType" method="post" >
    <fieldset>
        <h2>Localisation parente [site] (url)</h2>
        <p>
            Si vous ne choisissez pas un parent appartenant au site [<?=$allLocalisations_item['site']?>], 
            son emplacement sera répliqué dans le site [<?=$allLocalisations_item['site']?>] 
            pour permettre son administration.
        </p>
        <select name="localisationID">
            <? foreach( $allLocalisations as  $allLocalisations_item ) { ?>
                <option value="<?=$allLocalisations_item['id']?>">
                    [<?=$allLocalisations_item['site']?>]
                    <?=$allLocalisations_item['spacing'].$allLocalisations_item['name']?>
                    (<?=$allLocalisations_item['url']?>)
                </option>
            <? } ?>
        </select>
        <br/>
        <br/>
        
        <h2>Nom</h2>
        <p>
            Si laissée vide la valeur par défaut sera celle de la localisation d'origine. 
        </p>
        <input  type="text"
                name="name"
                id="name"
                value="<?=$baseLocalisation->name?>" />
        <br/>
        <br/>
        
        <h2>URL customisée</h2>
        <p>
            Cette valeur correspond au dernier mot de l'URL, elle sera nettoyée. <br/>
            Si laissée vide la valeur par défaut sera celle utilisée pour le nom (nettoyée). 
        </p>
        <input  type="text"
                name="customUrl"
                id="customUrl"
                value="" />
        <br/>
        <br/>
                
        <h2>Description</h2>
        <textarea name="description"><?=$baseLocalisation->description?></textarea>
    </fieldset>
    <br />
    
    <input  type="submit" 
            title="Créer un brouillon pour le nouveau contenu" 
            value="Publier" 
            name="createButton" 
            class="button" />
    <a href="<?=$cancelHref?>">
        <input  type="button" 
                title="Annuler nouvel emplacement" 
                value="Annuler" 
                name="cancelButton" 
                class="button" />
    </a>
</form>