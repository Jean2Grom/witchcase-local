<?php
    $this->addCssFile('boxes.css');
    $this->addJsFile('triggers.js');
    
    $this->addContextArrayItems( 'tabs', [
        'tab-current'       => [
            'selected'  => true,
            'iconClass' => "fas fa-list",
            'text'      => "List",
        ],
    ]);
    
    foreach( $profiles as $id => $profile ){
        $this->addContextArrayItems( 'tabs', [
            'tab-profile-'.$id       => [
                'text'      => $profile->name,
                'close'     => true,
                'hidden'    => true,
            ],
        ]);
    }
    
    $this->addContextArrayItems( 'tabs', [
        'tab-profile-add'       => [
            'iconClass' => "fas fa-plus",
            'text'      => "New",
        ],
    ]);
    
?>

<h2>User Profiles</h2> 
<p><em>Here you can manage permissions by handeling user profiles</em></p>

<?php include $this->getIncludeDesignFile('alerts.php'); ?>


<div class="tabs-target__item selected"  id="tab-current">
    <div class="box-container">
        <div><div class="box ">
            <h3>
                <i class="fas fa-users"></i> Profiles List
            </h3>
            <p><em>Filter by site here</em></p>
            
            <table>
                <thead>
                    <tr>
                        <th>Site</th>
                        <th>Name</th>
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
            
            <div class="box__actions">
                <button class="" 
                        id="create-profile-action"
                        data-href="<?=$createProfileHref ?>">
                    Create new profile
                </button>
            </div>
        </div></div>
    </div>
</div>

<?php foreach( $profiles as $id => $profile ): ?>
    <div class="tabs-target__item"  id="tab-profile-<?=$id ?>">
        <div class="box-container">
            <div><?php include $this->getIncludeDesignFile('view/profile.php'); ?></div>
            <div><?php include $this->getIncludeDesignFile('edit/profile.php'); ?></div>
        </div>
    </div>

<?php endforeach; ?>


<form id="delete-profile-form" method="post" >
    <input type="hidden" name="profile-id" value="" />
    <input type="hidden" name="action" value="delete-profile" />
</form>

<script>
$(document).ready(function()
{
    $('.show-details').click(function()
    {
        let hash = '#tab-profile-'+ $(this).data('id');
        
        $('a[href="'+hash+'"]').parent().show();
        triggerTabItem( hash );
    });
    
    
    $('button.policy-witch').on('click', function()
    {
        chooseWitch().then( (witchId) => { 
            if( witchId === false ){
                return;
            }
            
            $(this).html( readWitchName(witchId) );

            $('#new-mother-witch-id').val( witchId );
            $('#add-craft-witch-action').trigger('click');
        });
        
        return false;
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