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
            <p><em>Filter by site here
            <select id="profile-list-site-filter">
                <option value="">All sites</option>
                <option value="all">Global</option>
                <?php foreach( $websitesList as $website ): ?>
                    <option value="<?=$website->site ?>">
                        <?=$website->site ?>
                    </option>
                <?php endforeach; ?>
            </select>
            </em></p>
            
            <table>
                <thead>
                    <tr>
                        <th>Site</th>
                        <th>Name</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $profiles as $profile ): ?>
                        <tr class="profile-container profile-site-<?=$profile->site == '*'? 'all': $profile->site ?>" data-id="<?=$profile->id?>">
                            <td>
                                <span class="text-center"><?=$profile->site ?></span>
                            </td>
                            <td>
                                <a class="view-profile" data-id="<?=$profile->id?>">
                                    <?=$profile->name?>
                                </a>
                            </td>
                            <td>
                                <a class="edit-profile text-center">
                                    <i class="fa fa-pencil"></i>
                                    &nbsp;
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="box__actions">
                <button class="tabs__item__triggering" href="#tab-profile-add" >
                    <i class="fa fa-plus"></i>
                    Create new
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

<div class="tabs-target__item"  id="tab-profile-add">
    <div class="box-container">
        <div><?php include $this->getIncludeDesignFile('create/profile.php'); ?></div>
    </div>
</div>



<script>
$(document).ready(function()
{
    // List
    $('.view-profile').click(function()
    {
        let profileDom  = $(this).parents('.profile-container');
        let hash        = '#tab-profile-'+ $(profileDom).data('id');
        
        $('a[href="'+hash+'"]').parent().show();
        triggerTabItem( hash );
        
        $( hash ).find('.box.view__profile').show();
        $( hash ).find('.box.edit__profile').hide();
    });
    
    $('.edit-profile').click(function()
    {
        let profileDom  = $(this).parents('.profile-container');
        let hash        = '#tab-profile-'+ $(profileDom).data('id');
        
        $('a[href="'+hash+'"]').parent().show();
        triggerTabItem( hash );
        
        $( hash ).find('.box.view__profile').hide();
        $( hash ).find('.box.edit__profile').show();
    });
    
    $('#profile-list-site-filter').change(function()
    {
        let siteFilter = $(this).val();
        
        if( siteFilter === '' ){
            $('.profile-container').show();
        }
        else 
        {
            $('.profile-container').hide();
            $('.profile-container.profile-site-all').show();
            $('.profile-container.profile-site-'+siteFilter).show();
            
        }
        return false;
    });
    
    // Toggles
    $('.box.edit__profile').hide();
    $('button.view-edit-profile-toggle').click(function()
    {
        let profileId  = $(this).parents('.box').data('profile');
        
        $('.view__profile[data-profile="'+profileId+'"]').toggle();
        $('.edit__profile[data-profile="'+profileId+'"]').toggle();
        
        return false;
    });
    
    
    // Site 
    $('.profile-site').change(function()
    {
        let formDom = $(this).parents('form');
        let siteVal = $(this).val();
        
        if( siteVal === '*' ){
            siteVal = 'all';
        }
        
        $(formDom).find('.profile-site-displayed').hide();
        $(formDom).find('.profile-site-displayed.profile-site-'+siteVal).show();
    });
    
    // Policy
    $('.edit-profile-form').on('click', 'button.policy-witch',  function()
    {
        chooseWitch().then( (witchId) => {
            if( witchId === false ){
                return;
            }
            
            let witchName       = readWitchName(witchId);
            let policyDom       = $(this).parents('.policy-container');
            let witchBaseHref   = $(policyDom).find('.policy-witch-display').attr('href').split('?')[0];
            
            $(policyDom).find('button.policy-witch').hide();
            $(policyDom).find('.policy-witch-display').html( witchName ).attr('href', witchBaseHref + '?id=' + witchId).show();
            $(policyDom).find('.unset-policy-witch').show();
            $(policyDom).find('.policy-witch-set').show();
            
            $(policyDom).find('.policy-witch-id').val( witchId );
        });
        
        return false;
    });
    
    $('.edit-profile-form').on('click', '.unset-policy-witch',  function()
    {
        let policyDom       = $(this).parents('.policy-container');
        
        $(policyDom).find('button.policy-witch').show();
        $(policyDom).find('.policy-witch-display').hide();
        $(policyDom).find('.unset-policy-witch').hide();
        $(policyDom).find('.policy-witch-set').hide();        
        $(policyDom).find('.policy-witch-id').val('');
        
        return false;
    });
    
    // Remove / Add on Edit profile
    $('.edit-profile-form').on('click', '.policy-remove',  function()
    {
        let policyDom       = $(this).parents('.policy-container');
        let policyId        = $(policyDom).find('.policy-id').val();
        
        $(policyDom).find('.policy-deleted').val( policyId );
        $(policyDom).hide();
        
        return false;
    });
    
    $('.edit-profile-form').on('click', '.add-policy-action',  function()
    {
        let formDom         = $(this).parents('form.edit-profile-form');
        let newPolicy       = $(formDom).find('.policy-container').first().clone();
        let newPolicyIndex  = $(formDom).find('.policy-container.new-policy').length;
        
        $(newPolicy).find('.policy-id').val('new-' + newPolicyIndex);
        
        $(formDom).find('tbody').append( newPolicy );
        $(formDom).find('.policy-container').last().addClass('new-policy').show();
        
        return false;
    });
    
    
    $('.undo-profile-action').click(function()
    {
        let formDom         = $(this).parents('form.edit-profile-form');
        
        $(formDom).find('.unset-policy-witch').trigger('click');
        
        $(formDom).find('input, select, textarea').each(function( i, input )
        {
            if( $(input).data('init') !== undefined ){
                $(input).val( $(input).data('init') );
            }
        });
        
        $(formDom).find('.profile-site').trigger('change');        
        $(formDom).find('.new-policy').remove();
        
        $(formDom).find('.policy-deleted').each(function( i, input ){
            if( $(input).val() !== "" && $(input).val() > 0 )
            {
                $(input).val('');
                $(input).parents('.policy-container').show();
            }
            
        });
        
        $(formDom).find('.policy-witch-id').each(function( i, input )
        {
            let witchId = $(this).val();
            
            if( witchId !== "" )
            {
                let witchName       = readWitchName(witchId);
                let policyDom       = $(this).parents('.policy-container');
                let witchBaseHref   = $(policyDom).find('.policy-witch-display').attr('href').split('?')[0];
                
                $(policyDom).find('button.policy-witch').hide();
                $(policyDom).find('.policy-witch-display').html( witchName ).attr('href', witchBaseHref + '?id=' + witchId).show();
                $(policyDom).find('.unset-policy-witch').show();
                $(policyDom).find('.policy-witch-set').show();                
            }
        });
        
        $(formDom).find('.policy-witch-set input[type="checkbox"]').each(function( i, input ){
            if( $(this).parents('.policy-pattern').length === 0 ){
                $(this).prop('checked', ( $(this).data('init') === 1 ) );
            }
        });
        
        return false;
    });
    
    // Add/ Remove on Create profile
    $('.create-profile-form').on('click', '.add-policy-action',  function()
    {
        let formDom         = $(this).parents('form');
        let newPolicy       = $(formDom).find('.policy-container').first().clone();
        let newPolicyIndex  = $(formDom).find('.policy-container.new-policy').length;
        
        $(newPolicy).find('.policy-id').val('new-' + newPolicyIndex);
        
        $(formDom).find('tbody').append( newPolicy );
        $(formDom).find('.policy-container').last().addClass('new-policy').show();
        
        return false;
    });
    
    $('.create-profile-form').on('click', '.policy-remove',  function()
    {
        let policyDom       = $(this).parents('.policy-container');
        $(policyDom).hide();
        
        return false;
    });

    
    $('.create-profile-form').on('click', 'button.policy-witch',  function()
    {
        chooseWitch().then( (witchId) => {
            if( witchId === false ){
                return;
            }
            
            let witchName       = readWitchName(witchId);
            let policyDom       = $(this).parents('.policy-container');
            let witchBaseHref   = $(policyDom).find('.policy-witch-display').attr('href').split('?')[0];
            
            $(policyDom).find('button.policy-witch').hide();
            $(policyDom).find('.policy-witch-display').html( witchName ).attr('href', witchBaseHref + '?id=' + witchId).show();
            $(policyDom).find('.unset-policy-witch').show();
            $(policyDom).find('.policy-witch-set').show();
            
            $(policyDom).find('.policy-witch-id').val( witchId );
        });
        
        return false;
    });
    
    $('.create-profile-form').on('click', '.unset-policy-witch',  function()
    {
        let policyDom       = $(this).parents('.policy-container');
        
        $(policyDom).find('button.policy-witch').show();
        $(policyDom).find('.policy-witch-display').hide();
        $(policyDom).find('.unset-policy-witch').hide();
        $(policyDom).find('.policy-witch-set').hide();        
        $(policyDom).find('.policy-witch-id').val('');
        
        return false;
    });

    
    $('.reset-profile-action').click(function()
    {
        let formDom         = $(this).parents('form.create-profile-form');
        
        $(formDom).find('.profile-name').val('');
        $(formDom).find('.profile-site').val('*');
        $(formDom).find('.profile-site').trigger('change');
        $(formDom).find('.new-policy').remove();
        
        return false;
    });
    
    
});
</script>