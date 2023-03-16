<?php

$this->module->addCssFile('profiles.css');
?>

<h1>Voir Profil : <?=$profile->name?></h1>

<p class="left modified">
    ID :<?=$profile->id?>
</p>

<p class="left modified">
    Dernière modification le <?=$profile->datetime->frenchFormat(true)?>
</p>

<h2>Polices</h2>
<p>
    Le caractère "*" signifit tous les modules ou toutes les actions suivant son emplacement
</p>
<p>
    La localisation ne s'applique qu'à un site et ne s'étend pas à son emplacement d'administration
</p>
<p>
    Pour la validité des droits, "all" signifit que la police s'applique pour tous contenus, 
    "self" signifit que cette police ne s'applique que pour ses propres contenus utilisateur, 
    si un profile est désigné la police s'applique pour le groupe d'utilisateurs associés à ce profile
</p>

<?php foreach( $profile->polices as $police ): ?>
    <fieldset class="attributeField" >
        <legend>Police ID : <?=$police->id?></legend>
        
        <h3>Module - Action</h3>
        <p>
            <?=$police->module?> - <?=$police->action?>
        </p>
        
        <h3>Localisation [site]</h3>
        <p>
            <?=$police->localisation_display?>
        </p>
        
        <h3>Validité des droits pour l'utilisateur</h3>
        <p>
            <?=$police->limitation_display?>
        </P>
    </fieldset>
<?php endforeach; ?>

<div id="action-controls">
    <a href="<?=$baseUri?>">
        <input  type="button" 
                title="Revenir à la liste des profiles" 
                value="Liste des profiles" 
                name="listButton" 
                class="button" />
    </a>
    <a href="<?=$modificationHref?>">
        <input  type="button" 
                title="Modifier ce profil" 
                value="Modifier" 
                name="modifyButton" 
                class="button" />
    </a>
</div>
