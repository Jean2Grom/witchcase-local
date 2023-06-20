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
        $('#witch__add-content').prop( 'disabled', ($(this).val() === '') );
        $('#get-existing-craft').prop( 'disabled', ($(this).val() !== '') );
    });
    
    
    $('#get-existing-craft').on('click', function()
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
            $('#add-position-action').trigger('click');
        });
    });    
    
    $('.cut-descendants').on('click', function()
    {
        chooseWitch({}, "Choose moving destination witch").then( (witchId) => { 
            if( witchId === false ){
                return;
            }

            //$('#new-mother-witch-id').val( witchId );
            //$('#add-position-action').trigger('click');
        });
    });    
});
