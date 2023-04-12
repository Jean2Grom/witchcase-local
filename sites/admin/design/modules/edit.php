<style>
    h2 {
        font-size: 1.3em;
        font-style: italic;
        font-weight: normal;
        margin-bottom: 20px;
    }
    fieldset {
        border-radius: 15px;
        margin-bottom: 15px;
        float: left;
        margin-right: 15px;
        padding-bottom: 20px;
        box-shadow: 5px 5px 5px #ccc;
    }
        fieldset legend {
            font-weight: bold;
        }
        fieldset p {
            font-style: italic;
            margin-top: 20px;
        }
    .fieldsets-container {
        display: table;
        float: left;
    }
    textarea {
        width: 350px;
        height: 100px;
        resize: none;
    }
    input[type=number]{
        width: 60px;
    }
    input#customUrl {
        width: 95%;
    }
    label{
        font-weight: bold;
        font-style: normal;
    }
    .site_selected {
        display: none;
    }
    .custom-url_selected {
        /*display: none;*/
    }
</style>
<h1>
    Edition de l'élément
</h1>

<?php if( $targetWitch ): ?>
    <h2>
        <?=$targetWitch->name ?>
    </h2>
<?php endif; ?>

<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<form id="edit-action" method="post" >
    <div class="fieldsets-container">
        <fieldset>
            <legend>Données de navigation</legend>

            <h3>Nom*</h3>
            <p class="alert-message error" style="display: none;">
                Vous devez impérativement donner un nom à votre élément. 
            </p>
            <input  type="text" value="<?=$targetWitch->name ?>" 
                    name="witch-name" id="witch-name" />

            <h3>Description</h3>
            <textarea name="witch-data" id="witch-data"><?=$targetWitch->data ?></textarea>

            <h3>Priorité</h3>
            <p>
                Vous pouvez modifier ici la priorité
            </p>
            <input  type="number" value="<?=$targetWitch->priority ?>" 
                    name="witch-priority" id="witch-priority" />
        </fieldset>

        <fieldset>
            <legend>Accessibilité navigateur & visibilité</legend>

            <p>
                Ici on détermine si l'élément est accessible depuis l'URL d'un site web, <br/>ainsi que le statut de l'élement.
            </p>

            <h3>Pour le Site :</h3>
            <select name="witch-site" id="witch-site">
                <option value="">
                    Elément inaccessible
                </option>
                <?php foreach( $websitesList as $site => $website ): ?>
                    <option <?=($targetWitch && $targetWitch->site == $site)? 'selected' :'' ?>
                            value="<?=$site ?>">
                        <?=$website->name ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="site_selected">
                <h3>URL</h3>
                <div class="custom-url_selected">
                    <input  type="text"
                            name="witch-custom-url"
                            id="customUrl"
                            value="<?=$targetWitch->url ?>" />
                    <p>
                        <input type="checkbox" id="witch-custom-url-from-root" name="witch-custom-url-from-root" checked />
                        <label for="witch-custom-url-from-root">Depuis la racine</label>
                        <br/>
                        Pour que l'URL soit générée à partir du dernier élément parent du site,
                        <br/>
                        veuillez décocher la case ci-dessus.
                    </p>
                </div>
                <p title="L'URL sera automatiquement générée à partir du nom de l'élément (nettoyé) et de son arborescence.">
                    <input type="checkbox" id="witch-automatic-url" name="witch-automatic-url" />
                    <label for="witch-automatic-url">Automatique</label>
                    <br/>
                    Pour générer automatiquement une URL, veuillez cocher la case ci-dessus.
                </p>
            </div>

            <h3>Visibilité Elément :</h3>
            <p>
                Un élément peut être visible bien que non accessible directement, <br/>
                en accédant à son parent par exemple.<br/>
                <!--Attention avec le statut "public", il peut rendre immédiatement votre élément visible.-->
            </p>
            <select name="witch-status" id="witch-status"  data-current="<?=$targetWitch->status ?>">
                <optgroup   class="global_selected" 
                            label="global">
                    <?php foreach( $statusGlobal as $statusKey => $statusLabel ): ?>
                        <option value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <?php foreach( $websitesList as $site => $website ): ?>
                    <optgroup   disabled="disabled" style="display: none;"
                                class="<?=$site ?>_selected" 
                                label="<?=$website->name ?>">
                        <?php foreach( $website->get('status') as $statusKey => $statusLabel ): ?>
                            <option value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
        </fieldset>
    </div>
    
    <fieldset class="site_selected">
        <legend>Module & Contexte</legend>
        
        <h3>Nom du module</h3>
        <p>
            Le fichier de traitements qui sera exécuté lorsqu'on accède à l'URL de l'élément,<br/>
            si laissé a vide, un traitement par défaut sera exécuté (s'il est défini).
        </p>
        
        <select name="witch-invoke" id="witch-invoke" data-current="<?=$targetWitch->invoke ?>">
            <option value="">Pas de module</option>
            
            <?php foreach( $websitesList as $site => $website ): ?>
                <optgroup   disabled style="display: none;"
                            class="<?=$site ?>_selected" 
                            label="<?=$website->name ?>">
                    <?php foreach( $website->listModules() as $moduleItem ): ?>
                        <option value="<?=$moduleItem ?>"><?=$moduleItem ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
        
        <h3>Nom du contexte</h3>
        <p>
            Pour spécifier la page où le module est affiché (cas spécifiques).<br/>
            Si laissé a vide, une page par défaut sera utilisée.
        </p>
        <select name="witch-context" id="witch-context" data-current="<?=$targetWitch->context?>">
            <option value="">Pas de contexte</option>

            <?php foreach( $websitesList as $site => $website ): ?>
                <optgroup   disabled="disabled" style="display: none;"
                            class="<?=$site ?>_selected" 
                            label="<?=$website->name ?>">
                    <?php foreach( $website->listContexts() as $contextItem ): ?>
                        <option value="<?=$contextItem ?>"><?=$contextItem ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </fieldset>
    <div class="clear"></div>
    
    <?php if( $targetWitch ): ?>
        <button class="" 
                id="save-witch-and-return-action">
            Sauvegarder et Quitter
        </button>
        <button class="" 
                id="save-witch-action">
            Sauvegarder
        </button>
    <?php endif; ?>
    
    <?php if( $cancelHref ): ?>
        <button class="" 
                id="cancel"
                data-href="<?=$cancelHref ?>">
            Annuler
        </button>
    <?php endif; ?>
</form>

<script>
$(document).ready(function()
{
    $('#witch-site').change(function()
    {
        let selectedSite        = $(this).val();
        
        let selectedStatusKey   = $('#witch-status').val();
        if( selectedStatusKey == '' ){
            selectedStatusKey = $('#witch-status').data('current');
        }
        
        let selectedModuleKey   = $('#witch-invoke').val();
        if( selectedModuleKey == '' ){
            selectedModuleKey = $('#witch-invoke').data('current');
        }
        
        let selectedContextKey   = $('#witch-context').val();
        if( selectedContextKey == '' ){
            selectedContextKey = $('#witch-context').data('current');
        }
        
        if( selectedSite == '' )
        {
            $('.site_selected').hide();
            selectedSite = 'global';
        }
        else {
            $('.site_selected').show();
        }
        
        $('#witch-status .'+selectedSite+'_selected option:first').prop('selected', true);
        $('#witch-status optgroup').prop('disabled', true).hide();
        $('#witch-status .'+selectedSite+'_selected').prop('disabled', false).show();
        $('#witch-status .'+selectedSite+'_selected option[value='+selectedStatusKey+']').prop('selected', true);
        
        $('#witch-invoke option[value=""]').prop('selected', true);
        $('#witch-invoke optgroup').prop('disabled', true).hide();
        $('#witch-invoke .'+selectedSite+'_selected').prop('disabled', false).show();
        $('#witch-invoke .'+selectedSite+'_selected option[value="'+selectedModuleKey+'"]').prop('selected', true);
        
        
        $('#witch-context option[value=""]').prop('selected', true);
        $('#witch-context optgroup').prop('disabled', true).hide();
        $('#witch-context .'+selectedSite+'_selected').prop('disabled', false).show();
        $('#witch-context .'+selectedSite+'_selected option[value="'+selectedContextKey+'"]').prop('selected', true);
    });
    
    $('#witch-automatic-url').change(function(){
        if( !$(this).prop('checked') ){
            $('.custom-url_selected').show();
        }
        else {
            $('.custom-url_selected').hide();
        }
    });
    
    $('#cancel').click(function(){
        window.location.href = $(this).data('href');
        return false;
    });
    
    $('#save-witch-action').click(function(){
        return save( "save-witch" );
    });

    $('#save-witch-and-return-action').click(function(){
        return save( "save-witch-and-return" );
    });
    
    $('#witch-site').trigger('change');
    
    function save( actionName )
    {
        if( $('#witch-name').val().trim() == '' )
        {
            $('#witch-name').prev( 'p' ).show();
            return false;
        }
        
        let action = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( actionName );
        
        $('#edit-action').append( action );
        
        return true;
    }

});
</script>    