$(document).ready( function(){
    $('.changeFile').change(function(){
        let filename = $(this).val().split(/(\\|\/)/g).pop();
        $(this).next('input[type="hidden"]').val( filename );
    });
    
    $('.deleteFile').click(function(){
        if( confirm('Are you sure to remove file ?') )
        {
            $(this).parents('.current-file-display').hide();
            $(this).parents('fieldset').find('input[type="hidden"]').val( '' );
        }
        
        return false;
    });    
});