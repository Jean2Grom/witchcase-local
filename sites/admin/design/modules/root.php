<style>
    .root-content__data {
        cursor: pointer;
        margin-bottom: 5px;
    }
    .root-content__data-edit {
        display: none;
        margin-bottom: 5px;
    }
        #witch__data {
            width: 100%;
            resize: none;
        }
    .root-content__daughters {
        float: left;
        border: 1px solid #ccc;
        border-radius: 10px;
        margin: 15px 15px 5px 0;
        padding: 10px;
        box-shadow: 5px 5px 5px #ccc;
    }
        .root-content__daughters h2 {
            font-size: 1.1em;
            margin-top: 5px;
        }
            .root-content__daughters table th {
                min-width: 100px;
                background-color: #eee;
            }
            .root-content__daughters table td {
                padding: 1px 10px;
            }
            .root-content__daughters table input {
                width: 60px;
            }
        .root-content__daughters__actions {
            margin: 20px 0px 10px 0;
            text-align: right;
        }

</style>

<div class="root-content">
    <h1>
        Tableau de bord
    </h1>
    
    <?php include $this->getIncludeDesignFile('alerts.php'); ?>
    
    <div class="root-content__data"><?=$this->witch->data?></div>
    
    <div class="root-content__data-edit">
        <textarea   name="witch__data" 
                    id="witch__data"></textarea>
        <button class="" id="witch__data-edit">Publier</button>
    </div>
    
    <div class="root-content__daughters">
        <h2>
            Liste des enfants
        </h2>
        
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
                            <?php if( !empty($daughter->invoke) && $daughter->hasTarget() ): ?>
                                Module & Contenu
                            <?php elseif( !empty($daughter->invoke) ): ?>
                                Module
                            <?php elseif( $daughter->hasTarget() ): ?>
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
        
        <div class="root-content__daughters__actions">
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
    <div class="clear"></div>
</div>

<form method="post" id="root-action"></form>

<script>
$(document).ready(function()
{
    $('body').click(function(e)
    {
        if( $(e.target).is('button') ){
            return;
        }
        
        if( $(e.target).is('.root-content__data') )
        {
            $('#witch__data').val( $('.root-content__data').html() );
            $('.root-content__data').hide();
            $('.root-content__data-edit').show();
        }
        else if( $(e.target).parents('.root-content__data-edit').length == 0 )
        {
            $('.root-content__data-edit').hide();
            $('.root-content__data').show();
        }
    });
    
    
    $('#witch__data-edit').click(function()
    {
        let rootData = $('#witch__data').val().trim();
        
        let input = $("<input>").attr("type", "hidden")
                        .attr("name", "data")
                        .val( rootData );
        
        $('#root-action').append(input);
        
        input = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( "edit-data" );
        
        $('#root-action').append(input);
        $('#root-action').submit();
    });
    
    $('#daughters__edit-priorities').click(function()
    {
        $('.priorities-input').each(function(index, input){
            $('#root-action').append( $(input).attr("type", "hidden") );
        });
        
        let action = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( "edit-priorities" );
        
        $('#root-action').append( action );
        $('#root-action').submit();
    });
    
    $('#witch__add-child').click(function(){
        window.location.href = $(this).data('href');
    });
});
</script>