<?php
    $this->addCssFile('boxes.css');
    
    $this->addContextArrayItems( 'standardContextTabs', [
        'tab-current'       => [
            'selected'  => true,
            'iconClass' => "fas fa-info",
            'text'      => "Witch Info",
        ],
        'tab-invoke-part'   => [
            'iconClass' => "fas fa-hand-sparkles",
            'text'      => "Invoke",
        ],        
        'tab-craft-part'    => [
            'iconClass' => "fas fa-mortar-pestle",
            'text'      => "Craft",
        ],        
    ]);
?>
<style>
    .box.content__info table td.label {
        min-width: 100px;
        background-color: #eee;
        font-weight: bold;
        text-align: center;
        padding: 5px;        
    }
    .box .box__actions{
        margin: 20px 0px 10px 0;
        text-align: right;
    }
</style>

<div class="tabs-target__item selected"  id="tab-current">
    <h1><?=$this->witch->name ?></h1>
    <p><em><?=$this->witch->data?></em></p>

    <?php include $this->getIncludeDesignFile('alerts.php'); ?>
    
    <div class="box-container">
        <div>
            <div class="box content__info">
                <h3>
                    <?=$targetWitch->name ?>
                </h3>

                <table>
                    <tr>
                        <td class="label">Nom</td>
                        <td class="value"><?=$targetWitch->name ?></td>
                    </tr>
                    <tr>
                        <td class="label">Description</td>
                        <td class="value"><?=$targetWitch->data ?></td>
                    </tr>
                    <tr>
                        <td class="label">ID</td>
                        <td class="value"><?=$targetWitch->id ?></td>
                    </tr>
                </table>

                <div class="box__actions">
                    <?php if( $upLink ): ?>
                        <button class="" 
                                data-confirm="Attention ! Vous allez également supprimer la sous-arborescence."
                                id="witch__delete">
                            Supprimer
                        </button>
                    <?php endif; ?>
                    <button class="" 
                            data-href="<?=$editCraftWitchHref ?>"
                            id="witch__edit">
                        Modifier
                    </button>
                </div>
            </div>
        </div>
        
        <div>
            <div class="box content__daughters">
                <h3>
                    Matriarcat
                </h3>
                <p><em>Position dans l'arborescence : mère et filles</em></p>
                <table>
                    <thead>
                        <tr>
                            <?php foreach( $subTree['headers'] as $header ): ?>
                                <th>
                                    <?=$header?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if( $upLink ): ?>
                            <tr>
                                <td>
                                    <a href="<?=$upLink ?>" 
                                       title="<?=$targetWitch->mother()->name ?>">
                                        <i class="fas fa-reply rotate-90"></i>
                                        <?=$targetWitch->mother()->name ?>
                                    </a>
                                </td>
                                <td>
                                    <?=$targetWitch->mother()->site ?>
                                </td>
                                <td>
                                    <?php if( !empty($targetWitch->mother()->invoke) && $targetWitch->mother()->hasCraft() ): ?>
                                        Module & Contenu
                                    <?php elseif( !empty($targetWitch->mother()->invoke) ): ?>
                                        Module
                                    <?php elseif( $targetWitch->mother()->hasCraft() ): ?>
                                        Contenu
                                    <?php else: ?>
                                        Répertoire
                                    <?php endif; ?>
                                </td>
                                <td class="text-right"></td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach( $subTree['data'] as $daughter ): ?>
                            <tr>
                                <td>
                                    <a href="<?=$this->wc->website->baseUri."/view?id=".$daughter->id ?>">
                                        <?=$daughter->name ?>
                                    </a>
                                </td>
                                <td>
                                    <?=$daughter->site ?>
                                </td>
                                <td>
                                    <?php if( !empty($daughter->invoke) && $daughter->hasCraft() ): ?>
                                        Module & Contenu
                                    <?php elseif( !empty($daughter->invoke) ): ?>
                                        Module
                                    <?php elseif( $daughter->hasCraft() ): ?>
                                        Contenu
                                    <?php else: ?>
                                        Répertoire
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <input  class="priorities-input" 
                                            type="number"
                                            name="priorities[<?=$daughter->id ?>]" 
                                            value="<?=$daughter->priority ?>" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="box__actions">
                    <button class="" 
                            id="witch__add-child"
                            data-href="<?=$createElementHref?>">
                        Ajouter un enfant
                    </button>
                    <button class="" id="daughters__edit-priorities">
                        Changer les priorités
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tabs-target__item"  id="tab-invoke-part">
    <h2><em>Module invocation for</em></h2>
    
    <h3><em><?=$targetWitch->name ?></em></h3>
    <p><em><?=$targetWitch->data?></em></p>
    
    <div class="box-container">
        <div>
            <div class="box content__info">
                <h3>
                    Invoke
                </h3>

                <table>
                    <tr>
                        <td class="label">Module</td>
                        <td class="value"><?=$targetWitch->invoke ?></td>
                    </tr>
                    <tr>
                        <td class="label">Site</td>
                        <td class="value"><?=$targetWitch->site ?></td>
                    </tr>
                    <tr>
                        <td class="label">URL</td>
                        <td class="value"><?=$targetWitch->url ?></td>
                    </tr>
                    <tr>
                        <td class="label">Statut</td>
                        <td class="value"><?=$targetWitch->status ?></td>
                    </tr>

                    <tr>
                        <td class="label">Context</td>
                        <td class="value"><?=$targetWitch->context ?></td>
                    </tr>

                </table>

                <div class="box__actions">
                    <?php if( $upLink ): ?>
                        <button class="" 
                                data-confirm="Attention ! Vous allez également supprimer la sous-arborescence."
                                id="witch__delete">
                            Supprimer
                        </button>
                    <?php endif; ?>
                    <button class="" 
                            data-href="<?=$editCraftWitchHref ?>"
                            id="witch__edit">
                        Modifier
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tabs-target__item"  id="tab-craft-part">
    <h2><em>Content craft for</em></h2>
    
    <h3><em><?=$targetWitch->name ?></em></h3>
    <p><em><?=$targetWitch->data?></em></p>
    
    <div class="box-container">
        <div>
            <div class="box content__target">
                <h3>
                    <?=!empty($targetWitch->craft())? ucfirst($targetWitch->craft()->structure->type): "Pas de contenu" ?>
                </h3>
                <?php if( empty($targetWitch->craft()) ): ?>
                    <form method="post" id="witch-add-new-content">
                        <select name="witch-content-structure" id="witch-content-structure">
                            <option value="">
                                Pas de contenu
                            </option>
                            <?php foreach( $structuresList as $structureData ): ?>
                                <option value="<?=$structureData['name']?>">
                                    <?=$structureData['name']?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    
                    <div class="box__actions">
                        <button id="witch__add-content" disabled
                                class="trigger-action"
                                data-action="add-content"
                                data-target="witch-add-new-content">
                            Ajouter contenu
                        </button>
                    </div>
                
                <?php else: ?>
                    <h4>
                        <?=$targetWitch->craft()->name ?>
                        <span class="content-structure-type">
                            [<?=$targetWitch->craft()->structure->name ?>]
                        </span>
                    </h4>
                    <?php foreach( $targetWitch->craft()->attributes as $attribute ): ?>
                        <fieldset>
                            <legend><?=$attribute->name?> [<?=$attribute->type?>]</legend>
                                <?php $attribute->display() ?>
                        </fieldset>
                        <div class="clear"></div>
                    <?php endforeach; ?>

                    <div class="box__actions">
                        <?php if( $targetWitch->craft()->structure->type === WC\Craft\Content::TYPE ): ?>
                            <button class="trigger-action"
                                    data-confirm="Etes vous sur de vouloir archiver le contenu ?"
                                    data-action="archive-content"
                                    data-target="view-action">
                                Archiver
                            </button>
                        <?php endif; ?>
                        <button class="" 
                                data-href="<?=$editCraftContentHref ?>"
                                id="content__edit">
                            Modifier
                        </button>
                        <button class="trigger-action"
                                data-confirm="Etes vous sur de vouloir supprimer le contenu ?"
                                data-action="delete-content"
                                data-target="view-action">
                            Supprimer
                        </button>
                        <!--button class="trigger-action"
                                data-action="edit-content"
                                data-target="view-action">
                            Editer
                        </button-->
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<form method="post" id="view-action"></form>

<script>
$(document).ready(function()
{
    $('#daughters__edit-priorities').click(function()
    {
        $('.priorities-input').each(function(index, input){
            $('#view-action').append( $(input).attr("type", "hidden") );
        });
        
        let action = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( "edit-priorities" );
        
        $('#view-action').append( action );
        $('#view-action').submit();
    });
    
    $('#witch__edit, #content__edit').click(function(){
        window.location.href = $(this).data('href');
    });
    
    $('#witch__add-child').click(function(){
        window.location.href = $(this).data('href');
    });
    
    $('#witch__delete').click(function(){
        if( confirm( $(this).data('confirm') ) )
        {
            let action = $("<input>").attr("type", "hidden")
                            .attr("name", "action")
                            .val( "delete-witch" );

            $('#view-action').append( action );
            $('#view-action').submit();
        }
    });
    
    $('#witch-content-structure').change(function(){
        $('#witch__add-content').prop( 'disabled', ($(this).val() === '') );
    });
    
    $('.trigger-action').click(function(){
        let data = $(this).data();
        if( data.action === undefined 
            ||  data.target === undefined 
            || (data.confirm !== undefined && !confirm( data.confirm ))
        ){
            return false;
        }
        
        let action = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( data.action );
        
        $('#' + data.target).append( action );
        $('#' + data.target).submit();
        
        return false;
    });
});
</script>