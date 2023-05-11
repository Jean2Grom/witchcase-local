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
        display: none;
    }
</style>
<h1>
    Création de votre nouvel élément
</h1>

<?php if( $motherWitch ): ?>
    <h2>
        depuis l'élément&nbsp;:
        <?=$motherWitch->name ?>
    </h2>
<?php endif; ?>

<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<form id="create-action" method="post" >
    <div class="fieldsets-container">
        <fieldset>
            <legend>Données de navigation</legend>

            <h3>Nom*</h3>
            <p>
                Vous devez impérativement donner un nom à votre nouvel élément. 
            </p>
            <input  type="text" value="" 
                    name="new-witch-name" id="new-witch-name" />

            <h3>Description</h3>
            <textarea name="new-witch-data" id="new-witch-data"></textarea>

            <h3>Priorité</h3>
            <p>
                Vous pouvez indiquer dès maintenant la priorité
            </p>
            <input  type="number" value="0" 
                    name="new-witch-priority" id="new-witch-priority" />
        </fieldset>

        <fieldset>
            <legend>Accessibilité navigateur & visibilité</legend>

            <p>
                Ici on détermine si l'élément est accessible depuis l'URL d'un site web, <br/>ainsi que le statut de l'élement.
            </p>

            <h3>Pour le Site :</h3>
            <select name="new-witch-site" id="new-witch-site">
                <option value="">
                    Elément inaccessible
                </option>
                <?php foreach( $websitesList as $site => $website ): ?>
                    <option <?=($motherWitch && $motherWitch->site == $site)? 'selected' :'' ?>
                            value="<?=$site ?>">
                        <?=$website->name ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="site_selected">
                <h3>URL</h3>
                <p>
                    L'URL est automatiquement générée à partir du nom de l'élément (nettoyé) <br/>
                    et de son arborescence.<br/>
                    Pour personnaliser l'URL veuillez décocher la case ci-dessous.<br/>
                    <input type="checkbox" id="new-witch-automatic-url" name="new-witch-automatic-url" checked />
                    <label for="new-witch-automatic-url">Automatique</label>
                </p>
                <div class="custom-url_selected">
                    <input  type="text"
                            name="new-witch-custom-url"
                            id="customUrl"
                            value="" />
                    <p>
                        Cette valeur correspond au dernier mot de l'URL (nettoyé). <br/>
                        Pour qu'elle corresponde à l'URL depuis la racine du site (siteaccess),<br/>
                        veuillez cocher la case ci-dessous.<br/>
                        <input type="checkbox" id="new-witch-custom-url-from-root" name="new-witch-custom-url-from-root" />
                        <label for="new-witch-custom-url-from-root">Depuis la racine</label>
                    </p>
                </div>
            </div>

            <h3>Visibilité Elément :</h3>
            <p>
                Un élément peut être visible bien que non accessible directement, <br/>
                en accédant à son parent par exemple.<br/>
                <!--Attention avec le statut "public", il peut rendre immédiatement votre élément visible.-->
            </p>
            <select name="new-witch-status" id="new-witch-status">
                <optgroup class="global_selected" label="global">
                    <?php foreach( $statusGlobal as $statusKey => $statusLabel ): ?>
                        <option value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <?php foreach( $websitesList as $site => $website ): ?>
                    <optgroup disabled="disabled" style="display: none;" 
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
        
        <select name="new-witch-invoke" id="new-witch-invoke">
            <option value="">Pas de module</option>

            <?php foreach( $websitesList as $site => $website ): ?>
                <optgroup disabled="disabled" style="display: none;"
                          class="<?=$site ?>_selected" 
                          label="<?=$website->name ?>" >
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
        <select name="new-witch-context" id="new-witch-context">
            <option value="">Pas de contexte</option>

            <?php foreach( $websitesList as $site => $website ): ?>
                <optgroup disabled="disabled" style="display: none;"
                          class="<?=$site ?>_selected" 
                          label="<?=$website->name ?>" >
                    <?php foreach( $website->listContexts() as $contextItem ): ?>
                        <option value="<?=$contextItem ?>"><?=$contextItem ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </fieldset>
    
    <fieldset>
        <legend>Contenu</legend>
        <h3>Type de contenu :</h3>
        <select name="new-witch-structure">
            <option value="">
                Pas de contenu
            </option>
            <?php foreach( $structuresList as $structureData ): ?>
                <option value="<?=$structureData['name']?>">
                    <?=$structureData['name']?>
                </option>
            <?php endforeach; ?>
        </select>
    </fieldset>
    <div class="clear"></div>
    
    <?php if( $motherWitch ): ?>
        <button class="" 
                id="new-witch-action">
            Créer
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
    $('#new-witch-site').change(function()
    {
        let selectedSite        = $(this).val();
        
        let selectedStatusKey   = $('#new-witch-status').val();
        if( selectedStatusKey == '' ){
            selectedStatusKey = $('#new-witch-status').data('current');
        }
        
        let selectedModuleKey   = $('#new-witch-invoke').val();
        if( selectedModuleKey == '' ){
            selectedModuleKey = $('#new-witch-invoke').data('current');
        }
        
        let selectedContextKey   = $('#new-witch-context').val();
        if( selectedContextKey == '' ){
            selectedContextKey = $('#new-witch-context').data('current');
        }
        
        if( selectedSite == '' )
        {
            $('.site_selected').hide();
            selectedSite = 'global';
        }
        else {
            $('.site_selected').show();
        }
        
        $('#new-witch-status .'+selectedSite+'_selected option:first').prop('selected', true);
        $('#new-witch-status optgroup').prop('disabled', true).hide();
        $('#new-witch-status .'+selectedSite+'_selected').prop('disabled', false).show();
        $('#new-witch-status .'+selectedSite+'_selected option[value='+selectedStatusKey+']').prop('selected', true);
        
        $('#new-witch-invoke option[value=""]').prop('selected', true);
        $('#new-witch-invoke optgroup').prop('disabled', true).hide();
        $('#new-witch-invoke .'+selectedSite+'_selected').prop('disabled', false).show();
        $('#new-witch-invoke .'+selectedSite+'_selected option[value="'+selectedModuleKey+'"]').prop('selected', true);
        
        $('#new-witch-context option[value=""]').prop('selected', true);
        $('#new-witch-context optgroup').prop('disabled', true).hide();
        $('#new-witch-context .'+selectedSite+'_selected').prop('disabled', false).show();
        $('#new-witch-context .'+selectedSite+'_selected option[value="'+selectedContextKey+'"]').prop('selected', true);
    });
    
    $('#new-witch-automatic-url').change(function(){
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
    
    $('#new-witch-action').click(function()
    {
        if( $('#new-witch-name').val().trim() == '' )
        {
            $('#new-witch-name').prev( 'p' ).addClass( 'alert-message error' );
            return false;
        }
        
        let action = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( "create-new-witch" );
        
        $('#create-action').append( action );
        $('#create-action').submit();
    });
    
    $('#new-witch-site').trigger('change');
});
</script>    