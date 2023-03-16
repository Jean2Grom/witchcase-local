<h1>
    Voir Structure : <?=$structure->name?>
    <?php if( $structure->isArchive ): ?>
        [ARCHIVE]
    <?php endif; ?>
</h1>

<p class="left modified">
    Dernière modification le <?=$creationDateTime->frenchFormat(true)?>
</p>

<h2>Attributs</h2>

<?php foreach( $attributes as $attributeName => $attributeData ): ?>
    <fieldset class="attributeField">
        <legend><?=$attributeName ?> [<?=($attributeData['class'])::ATTRIBUTE_TYPE ?>]</legend>
        <p>
            <h3>Nom</h3>
            <?=$attributeName ?>
        </p>
        <p>
            <h3>Type</h3>
            <?=($attributeData['class'])::ATTRIBUTE_TYPE ?>
        </p>
        
        <?php if( !empty(($attributeData['class'])::PARAMETERS) ): foreach( ($attributeData['class'])::PARAMETERS as $name => $parameterData ): ?>
            <p>
                <h3><?=$name?></h3>
                <?=$parameterData['value']?>
                <?php $this->wc->debug->dump($parameterData) ?>
            </p>
        <?php endforeach; endif; ?>
    </fieldset>
<?php endforeach; ?>

<?php if( count($archivedAttributes) ): ?>
    <div id="archivedAttributes" style="display: none;">
        <?php foreach( $archivedAttributes as $attribute ): ?>
            <fieldset class="archivedAttributeField">
                <legend>ARCHIVED <?=$attribute->name?> [<?=$attribute->type?>]</legend>
                <p>
                    <h3>Nom</h3>
                    <?=$attribute->name?>
                </p>
                <p>
                    <h3>Type</h3>
                    <?=$attribute->type?>
                </p>

                <?php if( count($attribute->parameters) > 0 ): ?>
                    <?php foreach( $attribute->parameters as $parameter): ?>
                        <p>
                            <h3><?=$name?></h3>
                            <?=$parameterData['value']?>
                        </p>
                    <?php endforeach; ?>
                <?php endif; ?>

            </fieldset>
        <?php endforeach; ?>
        
        <div id="hideArchivesHref" >
            <span onclick="javascript: hideArchivedAttributes();">
                Cacher les attributs archivés
            </span>
        </div>
    </div>
    
    <div id="showArchivesHref" >
        <span onclick="javascript: showArchivedAttributes();">
            Voir les attributs archivés
        </span>
    </div>
<?php endif; ?>

<br/>
<div id="action-controls">
    <a href="<?=$baseUri?>">
        <input  type="button" 
                title="Revenir à la liste des structures" 
                value="Liste des structures" 
                name="listButton" 
                class="button" />
    </a>
    
    <?php if( !$structure->isArchive ): ?>
        <a href="<?=$modificationHref?>">
            <input  type="button" 
                    title="Modifier cette structure" 
                    value="Modifier" 
                    name="modifyButton" 
                    class="button" />
        </a>
    <?php endif; ?>
    
</div>
