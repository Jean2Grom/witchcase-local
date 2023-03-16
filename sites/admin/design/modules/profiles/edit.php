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
        .unset-profile-witch {
            cursor: pointer;
            display: none;
        }
    .profile-police {
        font-size: 0.9em;
    }
</style>

<h1>
    Modifier le Profil utilisateur : 
    <?=$targetProfile->name ?>
</h1>

<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<form id="edit-profile-form" method="post" >
    <div class="fieldsets-container">
        <fieldset>
            <legend>Données générales</legend>

            <h3>Nom*</h3>
            <p>
                Vous devez impérativement donner un nom à votre nouveau profil. 
            </p>
            <input  type="text" value="<?=$targetProfile->name ?>" 
                    name="profile-name" id="profile-name" />

            <h3>Pour le Site :</h3>
            <select name="profile-site" id="profile-site">
                <option value="*">
                    Tous les sites
                </option>
                <?php foreach( $websitesList as $site => $website ): ?>
                    <option <?=($targetProfile->site == $site)? 'selected': '' ?>
                            value="<?=$site ?>">
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

        <fieldset id="new-police-blank-pattern" class="profile-police">
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

            <select name="profile-module[]" class="profile-module">
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
            
            <select name="profile-status[]" class="profile-status">
                <option selected value="-1">Accès a tout statut</option>
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

            <button class="profile-witch" 
                    data-unset="Pas de position choisie">
                Pas de position choisie
            </button>
            
            <a class="unset-profile-witch">
                <i class="fa fa-times"></i>
            </a>
            
            <input  type="hidden" value="0" 
                    name="profile-witch-id[]" class="profile-witch-id" />

            <p>
                <input type="checkbox" 
                       class="profile-witch-parents" 
                       id="profile-witch-parents#0"
                       name="profile-witch-parents[]" />
                <label for="profile-witch-parents#0">Parents</label>
            </p>
            <p>
                <input type="checkbox" checked 
                       class="profile-witch-children" 
                       id="profile-witch-children#0" 
                       name="profile-witch-children[]" />
                <label for="profile-witch-children#0">Enfants</label>
            </p>
            <p>
                <input type="checkbox" checked 
                       class="profile-witch-included" 
                       id="profile-witch-included#0" 
                       name="profile-witch-included[]" />
                <label for="profile-witch-included#0">Position incluse</label>
            </p>
        </fieldset>
    
        <div class="clear"></div>
    
        <div id="policies-container">
            <?php foreach( array_values($targetProfile->policies) as $key => $police ): ?>
                <?php $i = $key + 1 ?>
                <fieldset id="police-<?=$i ?>" class="profile-police">
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

                    <select name="profile-module[]" class="profile-module">
                        <option value="*">Tous les modules</option>
                        <optgroup <?=($targetProfile->site != '*')? 'disabled="disabled" style="display: none;"': '' ?>
                                  class="global_selected" 
                                  label="Pour tous les sites" >
                            <?php foreach( $globalModulesList as $moduleItem ): ?>
                                <option <?=($targetProfile->site == '*' && $police->module == $moduleItem)? 'selected': '' ?>
                                        value="<?=$moduleItem ?>"><?=$moduleItem ?></option>
                            <?php endforeach; ?>
                        </optgroup>

                        <?php foreach( $websitesList as $site => $website ): ?>
                            <optgroup <?=($targetProfile->site != $site)? 'disabled="disabled" style="display: none;"': '' ?>
                                      class="<?=$site ?>_selected" 
                                      label="<?=$website->name ?>" >
                                <?php foreach( $website->listModules() as $moduleItem ): ?>
                                    <option <?=($targetProfile->site == $site && $police->module == $moduleItem)? 'selected': '' ?>
                                            value="<?=$moduleItem ?>"><?=$moduleItem ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>

                    <h3>Limite par statut</h3>
                    <p>
                        La liste ordonnée des statuts jusqu'auquel le profil aura accès,<br/>
                        public est le plus restrictif.
                    </p>

                    <select name="profile-status[]" class="profile-status">
                        <option <?=($police->status == '*' )? 'selected': '' ?>
                                value="-1">Accès a tout statut</option>
                        <optgroup   <?=($targetProfile->site != '*')? 'disabled="disabled" style="display: none;"': '' ?>
                                    class="global_selected" label="global">
                            <?php foreach( $statusGlobal as $statusKey => $statusLabel ): ?>
                                <option <?=($targetProfile->site == '*' && $police->status == $statusKey )? 'selected': '' ?>
                                        value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                        <?php foreach( $websitesList as $site => $website ): ?>
                            <optgroup <?=($targetProfile->site != $site)? 'disabled="disabled" style="display: none;"': '' ?>
                                      class="<?=$site ?>_selected" 
                                      label="<?=$website->name ?>">
                                <?php foreach( $website->status as $statusKey => $statusLabel ): ?>
                                    <option <?=($targetProfile->site == $site && $police->status == $statusKey )? 'selected': '' ?>
                                            value="<?=$statusKey ?>"><?=$statusLabel ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>

                    <h3>Position</h3>
                    <p>
                        Pour limiter cette police de droits à une portion de l'arborescence
                    </p>

                    <button class="profile-witch" 
                            data-unset="Pas de position choisie">
                        <?=($police->position !== false)? $police->positionName: 'Pas de position choisie' ?>
                    </button>

                    <a  <?=($police->position !== false)? 'style="display: inline-block;"': '' ?>
                        class="unset-profile-witch">
                        <i class="fa fa-times"></i>
                    </a>

                    <input  type="hidden" value="<?=($police->position !== false)? $police->positionId: '0' ?>" 
                            name="profile-witch-id[]" class="profile-witch-id" />

                    <p>
                        <input type="checkbox" 
                               <?=($police->position_rules["ancestors"])? 'checked': '' ?>
                               value="<?=$i ?>"
                               class="profile-witch-parents" 
                               id="profile-witch-parents#<?=$i?>"
                               name="profile-witch-parents[]" />
                        <label for="profile-witch-parents#<?=$i?>">Parents</label>
                    </p>
                    <p>
                        <input type="checkbox"  
                               <?=($police->position_rules["descendants"])? 'checked': '' ?>
                               value="<?=$i ?>"
                               class="profile-witch-children" 
                               id="profile-witch-children#<?=$i?>" 
                               name="profile-witch-children[]" />
                        <label for="profile-witch-children#<?=$i?>">Enfants</label>
                    </p>
                    <p>
                        <input type="checkbox"
                               <?=($police->position_rules["self"])? 'checked': '' ?>
                               value="<?=$i ?>"
                               class="profile-witch-included" 
                               id="profile-witch-included#<?=$i?>" 
                               name="profile-witch-included[]" />
                        <label for="profile-witch-included#<?=$i?>">Position incluse</label>
                    </p>
                </fieldset>
            
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="clear"></div>

    <?php if( $targetProfile ): ?>
        <button class="" 
                id="edit-profile-and-return-action">
            Modifier et Quitter
        </button>

        <button class="" 
                id="edit-profile-action">
            Modifier
        </button>
    <?php endif; ?>

    <button class="" 
            id="cancel"
            data-href="<?=$this->wc->website->baseUri."/profiles" ?>">
        Annuler
    </button>

    
</form>

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
    $('#profile-site').change(function()
    {
        let selectedSite        = $(this).val();
        if( selectedSite == '*' ){
            selectedSite = 'global';
        }
        
        $('.profile-police').each(function(i, police){
            let selectedModuleKey   = $(police).find('.profile-module').val();
            if( selectedModuleKey == '' ){
                selectedModuleKey = $(police).find('.profile-module').data('current');
            }
            
            $(police).find('.profile-module option[value="*"]').prop('selected', true);
            $(police).find('.profile-module optgroup').prop('disabled', true).hide();
            $(police).find('.profile-module .'+selectedSite+'_selected').prop('disabled', false).show();
            $(police).find('.profile-module .'+selectedSite+'_selected option[value="'+selectedModuleKey+'"]').prop('selected', true);
            
            let selectedStatusKey   = $(police).find('.profile-status').val();
            if( selectedStatusKey == '' ){
                selectedStatusKey = $(police).find('.profile-status').data('current');
            }
            
            $(police).find('.profile-status option[value="-1"]').prop('selected', true);
            $(police).find('.profile-status optgroup').prop('disabled', true).hide();
            $(police).find('.profile-status .'+selectedSite+'_selected').prop('disabled', false).show();
            $(police).find('.profile-status .'+selectedSite+'_selected option[value="'+selectedStatusKey+'"]').prop('selected', true);
        });
    });
    
    $('.profile-status').each(function(i, status){
        $(status).val( $(status).find('option[selected]').attr('value') );
    });
    
    // Add new police
    $('#add-police-profile').click( function(){
        let newPolice =  $('#new-police-blank-pattern').clone();
        
        let i = $('.profile-police').length;
        
        updatePoliceIncrement( newPolice, i );
        
        $('#policies-container').append( newPolice );
        
        return false;
    });
    
    // Delete police
    $('#policies-container').on('click', '.delete-police', function(){
        $(this).parents('.profile-police').remove();
        
        $('.profile-police').each(function(i, police){
            if( $(police).attr('id') != 'new-police-blank-pattern' ){
                updatePoliceIncrement( police, i );
            }
        });
    });
    
    function updatePoliceIncrement( policeDOM, i )
    {
        $(policeDOM).attr('id', 'police-'+i);
        $(policeDOM).find('.profile-witch-parents').val( i );
        $(policeDOM).find('.profile-witch-children').val( i );
        $(policeDOM).find('.profile-witch-included').val( i );
        
        $.each(['parents', 'children', 'included'], function(j, part){
            let oldId    = $(policeDOM).find('.profile-witch-'+part).attr( 'id' );
            let labelDom = $(policeDOM).find('label[for="'+oldId+'"]');
            let idBuffer = oldId.split('#');
            let newId    = idBuffer[0] + '#' + i;

            $(policeDOM).find('.profile-witch-'+part).attr( 'id', newId );
            $(labelDom).attr('for', newId);
        });
        
        return;
    }
    
    // Open popin for witch selection for policy
    $('#policies-container').on('click', '.profile-witch', function(){
        $(this).parents('.tabs-target__item').children().not('#choose-witch').css('filter', "blur(4px)");
        let targetId = $(this).parents('.profile-police').attr('id');
        $('#choose-witch-target').val( targetId );
        $('#choose-witch').show();
        
        return false;
    });
    
    // Unset policy witch
    $('#policies-container').on('click', '.unset-profile-witch', function(){
        $(this).parents('.profile-police').find('.profile-witch-id').val( '0' );
        let label = $(this).parents('.profile-police').find('.profile-witch').data('unset');
        $(this).parents('.profile-police').find('.profile-witch').html( label );
        
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
        
        $('#'+targetId).find('.profile-witch-id').val( witchId );
        $('#'+targetId).find('.profile-witch').html( witchLabel );
        $('#'+targetId).find('.unset-profile-witch').show();
        
        $('#choose-witch .close').trigger('click');
        
        return false;
    });
    
    // Policy witch popin navigation
    breadcrumb = [ breadcrumb[0] ];
    
    $('#choose-witch').on('click',  '.arborescence-level__witch__daughters-display', function(){
        let levelTarget = $(this).parents('.arborescence-menu-container');
        
        $(levelTarget).animate({scrollLeft: $(levelTarget).find('.arborescence-level').last().position().left}, 500);
    });
    
    
    $('#edit-profile-action').click(function(){
        return save( "edit-profile" );
    });

    $('#edit-profile-and-return-action').click(function(){
        return save( "edit-profile-and-return" );
    });

    function save( actionName )
    {
        if( $('#profile-name').val().trim() == '' )
        {
            $('#profile-name').prev( 'p' ).addClass( 'alert-message error' );
            return false;
        }
        
        let action = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( actionName );
        
        $('#edit-profile-form').append( action );
        
        return true;
    }
    
    // cancel creation
    $('#cancel').click(function(){
        window.location.href = $(this).data('href');
        return false;
    });
    
});
</script>    