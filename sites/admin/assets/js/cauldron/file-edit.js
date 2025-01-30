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
        
        container.querySelectorAll( 
            '.file-input .upload-file-input, .file-input .move-file-input' 
        ).forEach(
            input =>  input.addEventListener( 'change', () => {        
                //let filename = input.files[0].name;
                let filename = input.value.split(/(\\|\/)/g).pop();
                
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
                    if( input.value === '' )
                    {
                        input.value = filename.substring(0, filename.lastIndexOf('.'));

                        if( input.value === '' ){
                            input.value = filename;
                        }
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
                
                container.querySelectorAll( 
                    '.file-input .upload-file-input, .file-input .move-file-input' 
                ).forEach( input => {

                    let filename    = input.value.split(/(\\|\/)/g).pop();
                    if( filename === '' ){
                        return;
                    }

                    input.value     = '';

                    container.querySelectorAll('input.name-file-input').forEach( nameInput => {
                        if( nameInput.value === filename
                            || nameInput.value === filename.substring(0, filename.lastIndexOf('.'))  
                        ){
                            nameInput.value = '';
                        }
                        else if( !input.classList.contains('move-file-input') )
                        {
                            nameInput.select();
                            nameInput.focus();                
                        }
                    });

                    if( input.classList.contains('move-file-input') ){
                        input.focus();                
                    }
                });
            })
        );
    });
});
