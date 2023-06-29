$(document).ready(function()
{
    $('.edit__witch-info').hide();
    $('button.view-edit-info-toggle').click(function(){
        $('.view__witch-info').toggle();
        $('.edit__witch-info').toggle();
        
        $('.create__witch').hide();
        $('.view__daughters').show();        
    });
    
    $('.create__witch').hide();
    $('button.view-daughters__create-witch__toggle').click(function(){
        $('.view__witch-info').show();
        $('.edit__witch-info').hide();
        
        $('.create__witch').toggle();
        $('.view__daughters').toggle();
    });
    
    $('.edit__witch-invoke').hide();
    $('button.view-witch-invoke__edit-witch-invoke__toggle').click(function(){
        $('.view__witch-invoke').toggle();
        $('.edit__witch-invoke').toggle();
    });
    
    $('button.edit-info-reinit').click(function(){
        $('.edit__witch-info input, .edit__witch-info textarea').each(function( i, input ){
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
