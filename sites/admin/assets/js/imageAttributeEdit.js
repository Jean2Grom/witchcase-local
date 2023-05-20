$(document).ready( function(){
    $('.changeImage').change(function(){
        let filename = $(this).val().split(/(\\|\/)/g).pop();
        $(this).next('input[type="hidden"]').val( filename );
    });
    
    $('.deleteImage').click(function(){
        if( confirm('Êtes vous sûr ?') )
        {
            $(this).parents('.current-image-display').hide();
            $(this).parents('fieldset').find('input[type="hidden"]').val( '' );
        }
        
        return false;
    });
    
});