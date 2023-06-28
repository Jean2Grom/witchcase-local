$(document).ready(function()
{
    $('.trigger-href').click(function(){
        window.location.href = $(this).data('href');
        return false;
    });
    
    $('.trigger-action').click(function(){
        let data = $(this).data();
        if( data.action === undefined 
            ||  data.target === undefined 
            || (data.confirm !== undefined && !confirm( data.confirm ))
        ){
            return false;
        }
        
        let action = $("<input>").attr("type", "hidden")
                        .attr("name", "action")
                        .val( data.action );
        
        $('#' + data.target).append( action );
        $('#' + data.target).submit();
        
        return false;
    });
});