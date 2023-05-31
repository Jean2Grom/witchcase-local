$(document).ready(function()
{
    /*
     * LISTERNERS
     */
    $('.arborescence-menu-container').on('click', '.arborescence-level__witch__daughters-display', function()
    {
        let expand = !$(this).parents('.arborescence-level__witch').hasClass('selected');
        
        $(this).parents('.arborescence-level').nextAll().remove();
        $(this).parents('.arborescence-level').find('.arborescence-level__witch.selected').removeClass('selected');
        $(this).parents('.arborescence-level').find('.fa-chevron-right').removeClass('fa-chevron-right').addClass('fa-chevron-down');

        if( expand )
        {
            let witchId = $(this).parents('.arborescence-level__witch').data('id');
            let order   = treeData['daughters_orders'];
            let subTree = treeData;

            $('.arborescence-menu-container .arborescence-level .arborescence-level__witch.selected').each(function(index, element)
            {
                let subTreeId = $(element).data('id');

                if( subTreeId !== witchId )
                {
                    order   = subTree[ subTreeId ]['daughters_orders'];
                    subTree = subTree[ subTreeId ]['daughters'];
                }
            });

            order   = subTree[ witchId ]['daughters_orders'];
            subTree = subTree[ witchId ]['daughters'];
            
            $(this).parents('.arborescence-level__witch').addClass('selected');
            $(this).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
            
            addArborescenceLevel( subTree , order );
        }
    });

    /*
     * INIT ACTIONS
     */
    addArborescenceLevel( treeData );
    initArborescenceMenu( initPath );
    
    /*
     * FUNCTIONS
     */
    function addArborescenceLevel( subTree, order=false )
    {
        if( !order ){
            order = Object.keys(subTree);
        }
        
        let newArborescenceLevelHtml = '';
        newArborescenceLevelHtml        +=  '<div class="arborescence-level">';
        
        $.each(order, function( i, daughterId )
        {
            let daughterData = subTree[ daughterId ];
            
            newArborescenceLevelHtml    +=      '<div class="arborescence-level__witch ';
            if( daughterId === currentId ){
                newArborescenceLevelHtml    +=              'current ';
            }
            newArborescenceLevelHtml    +=              '" data-id="' + daughterId + '"> ';
            
            if( currentSite !== daughterData['site'] && daughterData['site'] !== '' ){
                newArborescenceLevelHtml    +=      '<span class="arborescence-level__witch__website">';
                newArborescenceLevelHtml    +=          daughterData['site'];
                newArborescenceLevelHtml    +=      '</span>';
            }
            
            if( daughterData['craft'] && daughterData['invoke'] ){
                newArborescenceLevelHtml    +=      '<i  class="fas fa-hat-wizard" ';
                newArborescenceLevelHtml    +=          'title="ID: ' + daughterData['id'] + ', content & executable"></i>';
            } else if( daughterData['craft'] ){
                newArborescenceLevelHtml    +=      '<i  class="fas fa-mortar-pestle" ';
                newArborescenceLevelHtml    +=          'title="ID: ' + daughterData['id'] + ', content"></i>';
            } else if( daughterData['invoke'] ){    
                newArborescenceLevelHtml    +=      '<i  class="fas fa-hand-sparkles" ';
                newArborescenceLevelHtml    +=          'title="ID: ' + daughterData['id'] + ', executable"></i>';
            } else {
                newArborescenceLevelHtml    +=      '<i class="fas fa-folder" ';
                newArborescenceLevelHtml    +=          'title="ID: ' + daughterData['id'] + '"></i>';
            }
            
            newArborescenceLevelHtml    +=          '<a class="arborescence-level__witch__name" ';
            newArborescenceLevelHtml    +=              'href="' + daughterData['uri'] + '" ';
            newArborescenceLevelHtml    +=              'title="' + daughterData['description'] + '">';
            newArborescenceLevelHtml    +=              daughterData['name'];
            newArborescenceLevelHtml    +=              '&nbsp;&nbsp;';
            newArborescenceLevelHtml    +=          '</a>';
            
            if( Object.keys(daughterData['daughters']).length > 0 ){
                newArborescenceLevelHtml    +=          '<span class="arborescence-level__witch__daughters-display">';
                newArborescenceLevelHtml    +=              '<i class="fas fa-chevron-down" title="Display daughters"></i>';
                newArborescenceLevelHtml    +=          '</span>';
            }

            newArborescenceLevelHtml    +=      '</div>';
        });
        newArborescenceLevelHtml        +=  '</div>';
        newArborescenceLevelHtml        +=  '<div class="clear"></div>';

        $('.arborescence-menu-container .clear').remove();
        
        $('.arborescence-menu-container').append(newArborescenceLevelHtml);

        $('.arborescence-menu-container.module').animate({
            scrollLeft: $('.arborescence-level').last().offset().left
        }, 700);
        
        return;
    }

    function initArborescenceMenu( initPath )
    {
        if( initPath === undefined || !initPath ){
            initPath = breadcrumb;
        }
        
        initPath.forEach( (pathWitchId, index) => {
            let domTree                 =   $('.arborescence-level');
            let daughterTriggerSelector =   '.arborescence-level__witch[data-id=' + pathWitchId + '] ';
            daughterTriggerSelector     +=  '.arborescence-level__witch__daughters-display';

            $(domTree[ index ]).find(daughterTriggerSelector).trigger('click');
        });

        return;
    }
});
