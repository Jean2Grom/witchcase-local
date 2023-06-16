async function chooseWitch()
{
    return new Promise( (resolve) => {
        $('#choose-witch').show();
        
        $('#choose-witch').on('click', '.close' ,function(){
            resolve( false );
        });
        
        $('#choose-witch').on('click', '.arborescence-level__witch__name', function(){
            let witchId = $(this).parents('.arborescence-level__witch').data('id');
            resolve( witchId );
        });
        
    }).then(( witchId ) => {
        $('#choose-witch').off( "click", ".arborescence-level__witch__name" );
        $('#choose-witch').off( "click", ".close" );
        $('#choose-witch').hide();
        
        return witchId;
    });
}

