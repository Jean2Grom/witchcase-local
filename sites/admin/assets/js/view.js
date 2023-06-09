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
    });
});
