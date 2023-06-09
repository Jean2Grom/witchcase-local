$(document).ready(function()
{
    $('#witch-site').change( witchSiteChange );
    $('#witch-auto-url').change( autoUrlChange );
    
    function witchSiteChange()
    {    
        $('.witch-invoke__part').hide();
        $('.site-selected').hide();
        
        let site = $('#witch-site').val();
        if( site !== '' )
        {
            $('.witch-invoke__part-' + site).show();
            $('.site-selected').show();            
        }
    }
    
    function autoUrlChange()
    {
        if( $('#witch-auto-url').prop('checked') ){
            $('.auto-url-disabled').hide();
        }
        else {
            $('.auto-url-disabled').show();
        }
    }
    
    $('button.edit-invoke-reinit').click(function(){
        $('.edit__invoke input, .edit__invoke select').each(function( i, input ){

            if( $(input).data('init') !== undefined ){
                $(input).val( $(input).data('init') );
            }
        });
        
        if( $('#witch-url').val() === '' )
        {
            $('#witch-full-url').prop('checked', false);
            $('#witch-auto-url').prop('checked', true);
        }
        else 
        {
            $('#witch-full-url').prop('checked', true);
            $('#witch-auto-url').prop('checked', false);
        }
        witchSiteChange();
        autoUrlChange();
    });
});
