document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll('.file-edit-container').forEach( container => {
        
        container.querySelectorAll('.switch-file-input-type a').forEach( 
            a => a.addEventListener( 'click', () => {
                if( a.classList.contains('selected') ){
                    return false;
                }
                
                container.querySelectorAll('.switch-file-input-type a.selected').forEach( 
                    elmt => elmt.classList.remove('selected') 
                );
                
                a.classList.add('selected');
                
                a.closest('.file-input').querySelectorAll('input').forEach( input => {
                    if( input.classList.contains( a.dataset.target ) ){
                        input.style.display = 'block';
                    }
                    else {
                        input.style.display = 'none';
                    }

                    input.select();
                    input.focus();
                });
            })
        );
        
        container.querySelectorAll( '.file-input .upload-file-input' ).forEach(
            input =>  input.addEventListener( 'change', () => {        
                let filename = input.files[0].name;
                
                container.querySelectorAll('.file-display .new-file-focus').forEach( 
                    span => span.innerHTML = filename
                );

                container.querySelectorAll('.file-display').forEach( 
                    elmnt => elmnt.style.display = 'block'
                );
                
                container.querySelectorAll('.file-input').forEach( 
                    elmnt => elmnt.style.display = 'none'
                );
                
                container.querySelectorAll('input.name-file-input').forEach( input => {
                    if( input.value === '' ){
                        input.value = filename.substring(0, filename.lastIndexOf('.'))
                    }
                    input.select();
                    input.focus();
                });
            })
        );

        container.querySelectorAll( '.remove-file' ).forEach(
            a =>  a.addEventListener( 'click', () => {
                if( !confirm('Remove file ?') ){
                    return false;
                }

                container.querySelectorAll( '.file-display .current-file-focus' ).forEach(
                    elmnt => elmnt.style.display = 'none'
                );

                container.querySelectorAll( '.file-display' ).forEach(
                    elmnt => elmnt.style.display = 'none'
                );
                
                container.querySelectorAll( '.file-input' ).forEach(
                    elmnt => elmnt.style.display = 'block'
                );
                
                container.querySelectorAll( '.file-input .upload-file-input' ).forEach( input => {
                    let filename    = input.files[0].name;
                    input.value     = '';

                    container.querySelectorAll('input.name-file-input').forEach( nameInput => {
                        if( nameInput.value === filename
                            || nameInput.value === filename.substring(0, filename.lastIndexOf('.'))  
                        ){
                            nameInput.value = '';
                        }
                        else 
                        {
                            nameInput.select();
                            nameInput.focus();                
                        }
                    });
                });
            })
        );

    });
});

$(document).ready( function(){
    $('.change-file').change(function(){
        let filename = $(this).val().split(/(\\|\/)/g).pop();
        $(this).parents('.change-file-container').next('input.file-input').val( filename );
        
        $(this).parents('.change-file-container').prev('.current-file-display').find('.new-file-target').html( filename );
        $(this).parents('.change-file-container').prev('.current-file-display').show();
        $(this).parents('.change-file-container').hide();
    });
    
    $('.delete-file').click(function(){
        if( confirm('Are you sure to remove file ?') )
        {
            $(this).parents('.current-file-display').find('.current-file-target').hide();
            $(this).parents('.current-file-display').hide();
            $(this).parents('.current-file-display').next('.change-file-container').show();
            $(this).parents('.current-file-display').next('.change-file-container').find('.change-file').val('');
            
            $(this).parents('.current-file-display').next('input.file-input').val( '' );
        }
        
        return false;
    });    
});