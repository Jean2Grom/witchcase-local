<style>
    .list-profiles-content, 
    .profile-content {
        float: left;
        border: 1px solid #ccc;
        border-radius: 10px;
        margin: 15px 15px 5px 0;
        padding: 10px;
        box-shadow: 5px 5px 5px #ccc;
    }
        .list-profiles-content table th, 
        .profile-content table th {
            min-width: 100px;
            background-color: #eee;
        }    
        .list-profiles-content table td, 
        .profile-content table td {
            padding: 0 10px;
        }    
        .list-profiles-content__actions,
        .profile-content__actions {
            margin: 20px 0px 10px 0;
            text-align: right;
        }
        .list-profiles-content h2,
        .profile-content h2 {
            font-size: 1.1em;
            margin-top: 5px;
        }
    .profile-content {
        display: none;
    }
        .profile-content .close {
            cursor: pointer;
            float: right;
            margin-right: 5px;
        }
    .show-details {
        cursor: pointer;
    }
</style>

<h1>Profils Utilisateurs</h1>

<?php include $this->getIncludeDesignFile('alerts.php'); ?>

<p>
    Voici la liste des profiles utilisateurs utilisés pour gérer les droits
</p>

<div class="list-profiles-content">
    <h2>Liste des profils</h2>
    <table>
        <thead>
            <tr>
                <th>Site</th>
                <th>Nom</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $profiles as $profile ): ?>
                <tr>
                    <td>
                        <?=$profile->site?>
                    </td>
                    <td>
                        <a class="show-details" data-id="<?=$profile->id?>" >
                            <?=$profile->name?>
                        </a>
                    </td>
                    <td class="text-right">
                        <a href="<?=$editProfileHref.$profile->id?>">
                            <i class="fa fa-pencil"></i>
                            &nbsp;
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="list-profiles-content__actions">
        <button class="" 
                id="create-profile-action"
                data-href="<?=$createProfileHref ?>">
            Ajouter un profil
        </button>
    </div>
</div>
<div class="clear"></div>

<?php foreach( $profiles as $profile ): ?>
    <div class="profile-content" id="profile_<?=$profile->id?>">
        <h2>
            <?=$profile->name?> / <?=$profile->site?>
            <a class="close">
                <i class="fa fa-times"></i>
            </a>
        </h2>
                    

        <table>
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Status</th>
                    <th>Position</th>
                    <th>Règles position</th>
                    <th>Spécifique</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $profile->policies as $policy ): ?>
                    <tr>
                        <td>
                            <?=$policy->module ?>
                        </td>
                        <td>
                            <?=$policy->statusLabel ?>
                        </td>
                        <td>
                            <?php if( !empty($policy->positionId) ): ?>
                                <a href="<?=$this->wc->website->baseUri."/view?id=".$policy->positionId ?>"
                                   target="_blank">
                                    <?=$policy->positionName ?? $policy->positionId ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?=$policy->position_rules['ancestors']? "Parents": "" ?>
                            <?=$policy->position_rules['self']? "Incluse": "" ?>
                            <?=$policy->position_rules['descendants']? "Enfants": "" ?>
                        </td>
                        <td>
                            <?=$policy->custom_limitation ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="profile-content__actions">
            <button data-id="<?=$profile->id ?>" 
                    class="delete-profile-action">
                Supprimer
            </button>
            <button data-href="<?=$editProfileHref.$profile->id ?>" 
                    class="edit-profile-action">
                Modifier
            </button>
        </div>
        
    </div>
<?php endforeach; ?>

<div class="clear"></div>

<form id="delete-profile-form" method="post" >
    <input type="hidden" name="profile-id" value="" />
    <input type="hidden" name="action" value="delete-profile" />
</form>

<script>
$(document).ready(function()
{
    $('.show-details').click(function()
    {
        let profileId = $(this).data('id');
        
        $('#profile_' + profileId).toggle();
    });
    
    $('.profile-content a.close').click(function(){
        $(this).parents('.profile-content').toggle();
    });
    
    
    $('#create-profile-action').click(function(){
        window.location.href = $(this).data('href');
    });
    
    $('.edit-profile-action').click(function(){
        window.location.href = $(this).data('href');
        return false;
    });
    
    $('.delete-profile-action').click(function(){
        let id = $(this).data('id');
        $('#delete-profile-form').find('input[name="profile-id"]').val( id );
        $('#delete-profile-form').submit();
    });
});
</script>