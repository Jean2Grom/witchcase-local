<?php

?>

<h1>
    Création de module
</h1>

<div class="errorMessages">
    <? foreach( $messages as $message ) { ?>
    <p><?=$message?></p>
    <? } ?>
</div>

<p>
    Ici vous pouvez créer un nouveau module, qui sera ensuite accessible dans le 
    module de création à l'emplacement que vous souhaitez dans le(s) site(s) que vous 
    avez déterminé.
</p>

<form method="post" >
    <fieldset>
        <h2>Nom</h2>
        <p>
            Vous devez impérativement donner un nom à votre nouveau module, 
            il sera utilisé pour les fichiers et la configuration. <br/>
            Ce nom sera nettoyé, évitez les caractères spéciaux.
        </p>
        <p>
            <input  type="text"
                    name="name"
                    id="name"
                    value="" />
        </p>
        
        <h2>Pour quel(s) site(s) ?</h2>
        <p>
            Dans la configuration, il doit être déclaré pour un site donné, 
            si ce site est hérité d'un autre, le module sera également hérité. <br/>
            Vous pouvez également l'assigner à tous les sites en choisissant "global".
        </p>
        <p>
            <select name="site">
                <? foreach( $sites as $site ) { ?>
                    <option value="<?=$site?>">
                        <?=$site?>
                    </option>
                <? } ?>
            </select>
        </p>
    </fieldset>
    <br/>
    
    <input type="submit"
           name="createButton"
           value="Créer le module" />
    
    <a href="<?=$cancelHref?>">
        <input type="button"
               name="cancelButton"
               value="Annuler" />
    </a>
</form>

