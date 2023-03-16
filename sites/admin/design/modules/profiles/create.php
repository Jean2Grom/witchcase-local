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
    .create-profile__actions {
        margin-top: 20px;
        text-align: right;
    }
    .delete-police {
        margin-right: 10px;
        float: right;
        cursor: pointer;
    }
    #new-police-blank-pattern {
        display: none;
    }
    #choose-witch .arborescence-menu-container .arborescence-level__witch.current {
        border: none;
    }
    #choose-witch .arborescence-menu-container .arborescence-level__witch.selected {
        background-color: #F3F3F3;
    }
    .tabs__item {
        display: none;
    }
        .tabs__item.selected {
            display: block;
        }
    #choose-witch {
        display: none;
        position: fixed;
        top: 30%;
        left: 30%;
        width: 40%;
        background-color: #FFF;
        padding: 0 15px 15px 15px;
        border: 1px solid #000;
        border-radius: 5px;
    }
        #choose-witch .close {
            cursor: pointer;
            float: right;
            margin-right: 5px;
        }
        .unset-new-profile-witch {
            cursor: pointer;
            display: none;
        }
    .new-profile-police {
        font-size: 0.9em;
    }
</style>

<h1>
    Créer un nouveau Profil utilisateur
</h1>

<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<form id="create-profile-action" method="post" >
    <div class="fieldsets-container">
        <fieldset>
            <legend>Données générales</legend>

            <h3>Nom*</h3>
            <p>
                Vous devez impérativement donner un nom à votre nouveau profil. 
            </p>
            <input  type="text" value="" 
                    name="new-profile-name" id="new-profile-name" />

            <h3>Pour le Site :</h3>
            <select name="new-profile-site" id="new-profile-site">
                <option value="*">
                    Tous les sites
                </option>
                <?php foreach( $websitesList as $site => $website ): ?>
                    <option value="<?=$site ?>">
                        <?=$website->name ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <div class="create-profile__actions">
                <button class="" 
                        id="add-police-profile">
                    Ajouter police
                </button>
            </div>
            
        </fieldset>

        <fieldset id="new-police-blank-pattern" class="new-profile-police">
            <legend>Police</legend>
            
            <a class="delete-police">
                <i class="fa fa-times"></i>
            </a>
            
            <p>
                Ici on détermine une règle de droits.
            </p>

            <h3>Nom du module</h3>
            <p>
                Le fichier de traitements/affichage qui sera exécuté lorsqu'on accède à l'URL de l'élément,<br/>
                si laissé a vide, un traitement par défaut sera exécuté (s'il est défini).
            </p>

            <select name="new-profile-module[]" class="new-profile-module">
                <option value="*">Tous les modules</option>
                <optgroup class="global_selected" 
                          label="Pour tous les sites" >
                    <?php foreach( $globalModulesList as $moduleItem ): ?>
                        <option value="<?=$moduleItem ?>"><?=$moduleItem ?></option>
                    <?php endforeach; ?>
                </optgroup>
                
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
            
            <h3>Limite par statut</h3>
            <p>
                La liste ordonnée des statuts jusqu'auquel le profil aura accès,<br/>
                public est le plus restrictif.
            </p>
            
            <select name="new-profile-status[]" class="new-profile-status">
                <option value="-1">Accès a tout statut</option>
                <optgroup class="global_selected" label="global">
                    <?php foreach( $statusGlobal as $statusKey => $statusLabel ): ?>
                        <option value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <?php foreach( $websitesList as $site => $website ): ?>
                    <optgroup disabled="disabled" style="display: none;" 
                              class="<?=$site ?>_selected" 
                              label="<?=$website->name ?>">
                        <?php foreach( $website->status as $statusKey => $statusLabel ): ?>
                            <option value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>

            <h3>Position</h3>
            <p>
                Pour limiter cette police de droits à une portion de l'arborescence
            </p>

            <button class="new-profile-witch" 
                    data-unset="Pas de position choisie">
                Pas de position choisie
            </button>
            
            <a class="unset-new-profile-witch">
                <i class="fa fa-times"></i>
            </a>
            
            <input  type="hidden" value="0" 
                    name="new-profile-witch-id[]" class="new-profile-witch-id" />

            <p>
                <input type="checkbox" 
                       class="new-profile-witch-parents" 
                       id="new-profile-witch-parents#0"
                       name="new-profile-witch-parents[]" />
                <label for="new-profile-witch-parents#0">Parents</label>
            </p>
            <p>
                <input type="checkbox" checked 
                       class="new-profile-witch-children" 
                       id="new-profile-witch-children#0" 
                       name="new-profile-witch-children[]" />
                <label for="new-profile-witch-children#0">Enfants</label>
            </p>
            <p>
                <input type="checkbox" checked 
                       class="new-profile-witch-included" 
                       id="new-profile-witch-included#0" 
                       name="new-profile-witch-included[]" />
                <label for="new-profile-witch-included#0">Position incluse</label>
            </p>
        </fieldset>
    
        <div class="clear"></div>
    
        <div id="policies-container"></div>
    </div>
    
    
</form>
<div class="clear"></div>

<button class="" 
        id="new-profile-action">
    Créer
</button>

<button class="" 
        id="cancel"
        data-href="<?=$this->wc->website->baseUri."/profiles" ?>">
    Annuler
</button>

<div id="choose-witch">
    <h3>
        Choisir un emplacement
        <a class="close">
            <i class="fa fa-times"></i>
        </a>
    </h3>
    
    <div class="arborescence-menu-container"></div>
    
    <input type="hidden" id="choose-witch-target" value="" />
</div>



<script>
$(document).ready(function()
{
    $('#new-profile-site').change(function()
    {
        let selectedSite        = $(this).val();
        if( selectedSite == '*' ){
            selectedSite = 'global';
        }
        
        $('.new-profile-police').each(function(i, police){
            let selectedModuleKey   = $(police).find('.new-profile-module').val();
            if( selectedModuleKey == '' ){
                selectedModuleKey = $(police).find('.new-profile-module').data('current');
            }
            
            $(police).find('.new-profile-module option[value="*"]').prop('selected', true);
            $(police).find('.new-profile-module optgroup').prop('disabled', true).hide();
            $(police).find('.new-profile-module .'+selectedSite+'_selected').prop('disabled', false).show();
            $(police).find('.new-profile-module .'+selectedSite+'_selected option[value="'+selectedModuleKey+'"]').prop('selected', true);
            
            let selectedStatusKey   = $(police).find('.new-profile-status').val();
            if( selectedStatusKey == '' ){
                selectedStatusKey = $(police).find('.new-profile-status').data('current');
            }
            
            $(police).find('.new-profile-status option[value="-1"]').prop('selected', true);
            $(police).find('.new-profile-status optgroup').prop('disabled', true).hide();
            $(police).find('.new-profile-status .'+selectedSite+'_selected').prop('disabled', false).show();
            $(police).find('.new-profile-status .'+selectedSite+'_selected option[value="'+selectedStatusKey+'"]').prop('selected', true);
        });
    });
    
    // Add new police
    $('#add-police-profile').click( function(){
        let newPolice =  $('#new-police-blank-pattern').clone();
        
        let i = $('.new-profile-police').length;
        
        updatePoliceIncrement( newPolice, i );
        
        $('#policies-container').append( newPolice );
        
        return false;
    });
    
    // Delete police
    $('#policies-container').on('click', '.delete-police', function(){
        $(this).parents('.new-profile-police').remove();
        
        $('.new-profile-police').each(function(i, police){
            if( $(police).attr('id') != 'new-police-blank-pattern' ){
                updatePoliceIncrement( police, i );
            }
        });
    });
    
    function updatePoliceIncrement( policeDOM, i )
    {
        $(policeDOM).attr('id', 'new-police-'+i);
        $(policeDOM).find('.new-profile-witch-parents').val( i );
        $(policeDOM).find('.new-profile-witch-children').val( i );
        $(policeDOM).find('.new-profile-witch-included').val( i );
        
        $.each(['parents', 'children', 'included'], function(j, part){
            let oldId    = $(policeDOM).find('.new-profile-witch-'+part).attr( 'id' );
            let labelDom = $(policeDOM).find('label[for="'+oldId+'"]');
            let idBuffer = oldId.split('#');
            let newId    = idBuffer[0] + '#' + i;

            $(policeDOM).find('.new-profile-witch-'+part).attr( 'id', newId );
            $(labelDom).attr('for', newId);
        });
        
        return;
    }
    
    // Open popin for witch selection for policy
    $('#policies-container').on('click', '.new-profile-witch', function(){
        $(this).parents('.tabs-target__item').children().not('#choose-witch').css('filter', "blur(4px)");
        let targetId = $(this).parents('.new-profile-police').attr('id');
        $('#choose-witch-target').val( targetId );
        $('#choose-witch').show();
        
        return false;
    });
    
    // Unset policy witch
    $('#policies-container').on('click', '.unset-new-profile-witch', function(){
        $(this).parents('.new-profile-police').find('.new-profile-witch-id').val( '0' );
        let label = $(this).parents('.new-profile-police').find('.new-profile-witch').data('unset');
        $(this).parents('.new-profile-police').find('.new-profile-witch').html( label );
        
        $(this).hide();
    });
    
    // Close select policy witch popin
    $('#choose-witch .close').click(function(){
        $(this).parents('.tabs-target__item').children().not('#choose-witch').css('filter', "blur(0)");
        $('#choose-witch-target').val('');
        $('#choose-witch').hide();
    });
    
    // Select witch for policy in popin
    $('#choose-witch').on('click', 'a.arborescence-level__witch__name', function(){
        let witchId     = $(this).parents('.arborescence-level__witch').data('id');
        let witchLabel  = $(this).html();
        let targetId    = $('#choose-witch-target').val();
        
        $('#'+targetId).find('.new-profile-witch-id').val( witchId );
        $('#'+targetId).find('.new-profile-witch').html( witchLabel );
        $('#'+targetId).find('.unset-new-profile-witch').show();
        
        $('#choose-witch .close').trigger('click');
        
        return false;
    });
    
    // Policy witch popin navigation
    breadcrumb = [ breadcrumb[0] ];
    
    $('#choose-witch').on('click',  '.arborescence-level__witch__daughters-display', function(){
        let levelTarget = $(this).parents('.arborescence-menu-container');
        
        $(levelTarget).animate({scrollLeft: $(levelTarget).find('.arborescence-level').last().position().left}, 500);
    });

    // Create action
    $('#new-profile-action').click(function(){
        if( $('#new-profile-name').val().trim() == '' )
        {
            $('#new-profile-name').prev( 'p' ).addClass( 'alert-message error' );
            return false;
        }
        
        let action = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( "create-new-profile" );
        
        $('#create-profile-action').append( action );
        $('#create-profile-action').submit();
        
    });
    
    // cancel creation
    $('#cancel').click(function(){
        window.location.href = $(this).data('href');
        return false;
    });
    
});
</script>    