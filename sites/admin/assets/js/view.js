$(document).ready(function()
{
    $('.edit__info').hide();
    $('button.view-edit-info-toggle').click(function(){
        $('.view__info').toggle();
        $('.edit__info').toggle();
        
        $('.create__info').hide();
        $('.view__position').show();        
    });
    
    $('.create__info').hide();
    $('button.position-create-toggle').click(function(){
        $('.view__info').show();
        $('.edit__info').hide();
        
        $('.create__info').toggle();
        $('.view__position').toggle();
    });
    
    $('.edit__invoke').hide();
    $('button.view-edit-invoke-toggle').click(function(){
        $('.view__invoke').toggle();
        $('.edit__invoke').toggle();
    });
    
    $('button.edit-info-reinit').click(function(){
        $('.edit__info input, .edit__info textarea').each(function( i, input ){
            if( $(input).data('init') !== undefined ){
                $(input).val( $(input).data('init') );
            }
        });
    });
    
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
    
    $('#add-craft-position').on('click', function()
    {
        chooseWitch().then( (witchId) => { 
            if( witchId === false ){
                return;
            }

            $('#new-mother-witch-id').val( witchId );
            $('#add-craft-position-action').trigger('click');
        });
    });    
    
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
