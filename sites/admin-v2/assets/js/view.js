$(document).ready(function()
{
    // Menu info edition behaviour
    $('.edit__witch-menu-info').hide();
    $('button.view-edit-menu-info-toggle').click(function(){
        $('.view__witch-menu-info').toggle();
        $('.edit__witch-menu-info').toggle();
        
        $('.edit__witch-info').hide();
        $('.view__witch-info').show();
        
        $('.create__witch').hide();
        $('.view__daughters').show();        
    });
    
    $('button.edit-menu-info-reinit').click(function(){
        $('.edit__witch-menu-info input, .edit__witch-menu-info textarea').each(function( i, input ){
            if( $(input).data('init') !== undefined ){
                $(input).val( $(input).data('init') );
            }
        });
    });
    
    
    // Add new witch behaviour
    $('.create__witch').hide();
    $('button.view-daughters__create-witch__toggle').click(function(){
        $('.create__witch').toggle();
        $('.view__daughters').toggle();
        
        $('.edit__witch-info').hide();
        $('.view__witch-info').show();
        
        $('.edit__witch-menu-info').hide();
        $('.view__witch-menu-info').show();        
    });       
    
    
    // Witch info edition behaviour
    $('.edit__witch-info').hide();
    $('button.view-edit-info-toggle').click(function(){
        $('.view__witch-info').toggle();
        $('.edit__witch-info').toggle();
        
        $('.edit__witch-menu-info').hide();
        $('.view__witch-menu-info').show();
        
        $('.create__witch').hide();
        $('.view__daughters').show();
    });
    
    
    $('#witch-site, .witch-invoke').change( witchInfoChange );
    $('#witch-auto-url').change( autoUrlChange );
    
    function witchInfoChange()
    {
        $('.witch-info__part').hide();
        $('#site-selected').hide();

        let site = $('#witch-site').val();
        $('.witch-info__part-' + site).show();

        if( site !== '' && $('#witch-invoke-' + site).val() !== '' ){
            $('#site-selected').show();
        }
    }    
    
    function autoUrlChange()
    {
        if( $('#witch-auto-url').prop('checked') ){
            $('.auto-url-disabled').hide();
        }
        else {
            $('.auto-url-disabled').show();
        }
    }
    
    $('button.edit-info-reinit').click(function(){
        $('.edit__witch-info input, .edit__witch-info select').each(function( i, input ){

            if( $(input).data('init') !== undefined ){
                $(input).val( $(input).data('init') );
            }
        });
        
        if( $('#witch-url').val() === '' )
        {
            $('#witch-full-url').prop('checked', false);
            $('#witch-auto-url').prop('checked', true);
        }
        else 
        {
            $('#witch-full-url').prop('checked', true);
            $('#witch-auto-url').prop('checked', false);
        }
        witchInfoChange();
        autoUrlChange();
    });
    
    
    // Craft part
    $('#witch-content-structure').change(function(){
        $('#witch-create-craft').prop( 'disabled', ($(this).val() === '') );
        $('#witch-get-existing-craft').prop( 'disabled', ($(this).val() !== '') );
    });    
    
    $('#witch-get-existing-craft').on('click', function()
    {
        chooseWitch({ craft: true }, "Choose importing craft witch").then( (witchId) => { 
            if( witchId === false ){
                return;
            }

            $('#imported-craft-witch').val( witchId );
            $('#import-craft-action').trigger('click');
        });
    });
    
    $('#add-craft-witch').on('click', function()
    {
        chooseWitch().then( (witchId) => { 
            if( witchId === false ){
                return;
            }

            $('#new-mother-witch-id').val( witchId );
            $('#add-craft-witch-action').trigger('click');
        });
    });    
    
    
    // Daughters cut/copy
    $('.cut-descendants').on('click', function()
    {
        $('#origin-witch').val( $(this).data('id') );
        
        chooseWitch({}, "Choose moving destination witch").then( (witchId) => { 
            if( witchId === false ){
                return;
            }
            
            $('#destination-witch').val( witchId );
            $('#move-witch-action').trigger('click');
        });
    });
    
    $('.copy-descendants').on('click', function()
    {
        $('#origin-witch').val( $(this).data('id') );
        
        chooseWitch({}, "Choose copy destination witch").then( (witchId) => { 
            if( witchId === false ){
                return;
            }
            
            $('#destination-witch').val( witchId );
            $('#copy-witch-action').trigger('click');
        });
    });    
});
