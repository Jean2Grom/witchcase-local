<?php

?>

<h1>MODULE  createsite</h1>


<div class="errorMessages">
    <? foreach( $messages as $message ) { ?>
    <p><?=$message?></p>
    <? } ?>
</div>

<p>
    Ici vous pouvez créer un nouveau site. 
</p>

<form method="post" >
    <fieldset>
        <h2>Nom</h2>
        <p>
            Vous devez impérativement donner un nom à votre nouveau site, 
            il sera utilisé pour les fichiers et la configuration. <br/>
            Ce nom sera nettoyé, évitez les caractères spéciaux.
        </p>
        <p>
            <input  type="text"
                    name="name"
                    id="name"
                    value="" />
        </p>
    </fieldset>
    <br/>

    <fieldset>
        <h2>Accès</h2>
        <p>
            Inscrivez ici les accès à ce nouveau site. <br/>
            Ne marquez pas ici le protocole (typiquement "http://"), mais simplement le host <br/>
            Vous pouvez mettre un host et un mot séparé par un '/', 
            par exemple : www.witch-case.com/sitealternatif<br/>
            Les majuscules ne sont pas prises en compte.<br/>
            Indiquez ici un accès par ligne, vous n'avez pas de limite.
        </p>
        <p>
            <textarea name="siteAccess"></textarea>
        </p>
    </fieldset>
    <br/>
    
    <fieldset>
        <h2>Héritage(s)</h2>
        <p>
            Si vous voulez que votre nouveau site récupère les controlleurs et/ou les
            apparences d'un autre site déjà présent sur Woody CMS, 
            indiquez le ici. 
        </p>
        <p>
            <select name="heritage">
                <option value="">
                    Pas d'hériatage
                </option>
                <? foreach( $sites as $site ) { ?>
                    <option value="<?=$site?>">
                        <?=$site?>
                    </option>
                <? } ?>
            </select>
        </p>
    </fieldset>
    <br/>
    
    <fieldset>
        <h2>Administration</h2>
        <p>
            Si votre nouveau site est un site d'administration pour un autre site présent sur Woody CMS, 
            veuilliez indiquer ici lesquels.<br/>
            Administrer un site comprend une ou plusieurs des phases de le vie du site :
            <ul>
                <li>
                    gestion des emplacements (arborescence), 
                </li>
                <li>
                    gestion des contenus (edition/ajout/suppression),
                </li>
                <li>
                     gestion des droits utilisateurs, 
                </li>
                <li>
                    structuration des contenus
                </li>
            </ul>
            Si votre nouveau site n'administre rien, ne cochez aucune des cases.
        </p>
        <p>
        <? foreach( $sites as $site ) { ?>
            <input  type="checkbox"
                    name="adminForSite[]"
                    value="<?=$site?>" /> <?=$site?>
        <? } ?>
        </p>
    </fieldset>
    
    <p>
        Donnez les informations sur le premier élément de votre site, 
        celui sur lequel on arrive lorsqu'on inscrit l'adresse du site (définie plus haut) 
        sur le navigateur. 
    </p>
    <fieldset>
        <legend>Page d'accueil</legend>
        <h2>Nom</h2>
        <p>
            Vous devez impérativement donner un nom à votre nouveau contenu. 
        </p>
        <p>
            <input  type="text"
                    name="nameHome"
                    id="nameHome"
                    value="" />
        </p>
        
        <h2>Description</h2>
        <p>
            <textarea name="description"></textarea>
        </p>
        
        <h3>Module</h3>
        <p>
            Pour créer un simple contenu visualisable, laissez le nom du module à 'view'
        </p>
        <p>
            <select name="moduleHome">
                <? foreach( $modulesDisplay as $moduleDisplayData ) { ?>
                    <option <? if($moduleDisplayData['default']){ ?>
                                selected="selected"
                            <? } ?>
                            value="<?=$moduleDisplayData['name']?>">
                        <?=$moduleDisplayData['name']?>
                    </option>
                <? } ?>
            </select>
        </p>
        
        <h2>Contenu</h2>
        <p>
            Certains modules ne nécessitent pas de contenu, dans ce cas vous devez signaler 
            l'absence de contenu.
        </p>
        
        <p>
            <select name="structureHome">
                <option value="">
                    Pas de contenu
                </option>
                <? foreach($structures as $structure) { ?>
                    <option value="<?=$structure['value']?>">
                        <?=$structure['label']?>
                    </option>
                    <br/>
                <? } ?>
            </select>
        </p>
    </fieldset>
    <br/>
    
    <input type="submit"
           name="createButton"
           value="Créer le site" />
    
    <a href="<?=$cancelHref?>">
        <input type="button"
               name="cancelButton"
               value="Annuler" />
    </a>
</form>
