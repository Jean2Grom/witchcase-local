async function chooseWitch( conditions={}, label="Choose witch" )
{
    return new Promise( (resolve) => {
        $('#choose-witch h3 span').html( label );
        $('#choose-witch').show();
        
        $('#choose-witch').on('click', '.close' ,function(){
            resolve( false );
        });
        
        $('#choose-witch').on('click', '.arborescence-level__witch__name', function()
        {
            let witch = $(this).parents('.arborescence-level__witch');
            
            let match = true;
            for( var data in conditions ) {
                if( $(witch).data( data ) !== conditions[ data ] ){
                    match = false;
                }
            }
            
            if( match ){
                resolve( $(witch).data('id') );                
            }            
        });
        
    }).then(( witchId ) => {
        $('#choose-witch').off( "click", ".arborescence-level__witch__name" );
        $('#choose-witch').off( "click", ".close" );
        $('#choose-witch').hide();
        
        return witchId;
    });
}

