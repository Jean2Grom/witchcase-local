<style>
    .rotate-90 {
        transform: rotate(90deg);
    }
    
    .view-content__info,
    .view-content__daughters,
    .view-content__target {
        float: left;
        border: 1px solid #ccc;
        border-radius: 10px;
        margin: 15px 15px 5px 0;
        padding: 10px;
        box-shadow: 5px 5px 5px #ccc;
    }
        .view-content__info table td.label {
            min-width: 100px;
            background-color: #eee;
            font-weight: bold;
            text-align: center;
            padding: 5px;
        }
        .view-content__info table td.value {
            min-width: 200px;
            max-width: 300px;
            padding: 5px;
            padding-left: 20px;
        }
        .view-content__daughters h2 {
            font-size: 1.1em;
            margin-top: 5px;
        }
            .view-content__daughters table th {
                min-width: 100px;
                background-color: #eee;
            }
            .view-content__daughters table td {
                padding: 1px 10px;
            }
            .view-content__daughters table input {
                width: 60px;
            }
        .view-content__info__actions,
        .view-content__daughters__actions,
        .view-content__target__actions {
            margin: 20px 0px 10px 0;
            text-align: right;
        }
        .view-content__target h4 {
            font-weight: normal;
            font-style: italic;
            text-align: right;
            margin: -15px 15px 10px 15px;
        }
        .view-content__target .content-structure-type {
            font-style: normal;
            font-weight: bold;
            margin-left: 5px;
            font-size: 0.9em;
        }
    #witch__add-content {
        margin-top: 15px;
    }
</style>
<div class="view-content">
    <h1><?=$this->witch->name ?></h1>
    <div class="view-content__data"><p><em><?=$this->witch->data?></em></p></div>
    
    <?php include $this->getIncludeDesignFile('alerts.php'); ?>
    
    <h2><em><?=$targetWitch->name ?></em></h2>
    <p><em><?=$targetWitch->data?></em></p>
    
    <div class="view-content__info">
        <h3>
            Infos
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
            <tr>
                <td class="label">Statut</td>
                <td class="value"><?=$targetWitch->status ?></td>
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
                <td class="label">Module</td>
                <td class="value"><?=$targetWitch->invoke ?></td>
            </tr>
            <tr>
                <td class="label">Contexte</td>
                <td class="value"><?=$targetWitch->context ?></td>
            </tr>
        </table>
        
        <div class="view-content__info__actions">
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
    
    <div class="view-content__daughters">
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
                               title="<?=$targetWitch->mother->name ?>">
                                <i class="fas fa-reply rotate-90"></i>
                                <?=$targetWitch->mother->name ?>
                            </a>
                        </td>
                        <td>
                            <?=$targetWitch->mother->site ?>
                        </td>
                        <td>
                            <?php if( !empty($targetWitch->mother->invoke) && $targetWitch->mother->hasCraft() ): ?>
                                Module & Contenu
                            <?php elseif( !empty($targetWitch->mother->invoke) ): ?>
                                Module
                            <?php elseif( $targetWitch->mother->hasCraft() ): ?>
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
        
        <div class="view-content__daughters__actions">
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
    
    <div class="view-content__target">
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
            <div class="clear"></div>
            
            <button id="witch__add-content" disabled
                    class="trigger-action"
                    data-action="add-content"
                    data-target="witch-add-new-content">
                Ajouter contenu
            </button>
            
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
            
            <div class="view-content__target__actions">
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
    <div class="clear"></div>
</div>

<div class="clear"></div>

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