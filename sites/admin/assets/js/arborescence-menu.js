const ArborescenceMenu = function ( key ) { 
    return {
        key: key,
        treeData: null,
        currentId: null,
        currentSite: null,
        breadcrumb: null,
        initPath: false,
        
        init: function( entries )
        {
            this.treeData       = entries.treeData;
            this.currentId      = entries.currentId;
            this.currentSite    = entries.currentSite;
            this.breadcrumb     = entries.breadcrumb;            
            
            this.addArborescenceLevel( this.treeData );
            
            this.open( this.initPath );
            
            $('#' + this.key + '.arborescence-menu-container.module').animate({
                scrollLeft: 999999, behavior: "instant"
            });
        },        
        open: function( initPath )
        {
            if( initPath === undefined || !initPath ){
                initPath = this.breadcrumb;
            }

            initPath.forEach( (pathWitchId, index) => {
                let domTree                 =   $('#' + this.key + ' .arborescence-level');
                let daughterTriggerSelector =   '.arborescence-level__witch[data-id=' + pathWitchId + '] ';
                daughterTriggerSelector     +=  '.arborescence-level__witch__daughters-display';
                
                this.toggle( $(domTree[ index ]).find(daughterTriggerSelector) );
            });

            return;
        },

        addArborescenceLevel: function( subTree, order=false )
        {
            if( !order ){
                order = Object.keys(subTree);
            }

            let newArborescenceLevelHtml = '';
            newArborescenceLevelHtml        +=  '<div class="arborescence-level">';
            
            let current = this.currentId;
            $.each(order, function( i, daughterId )
            {
                let daughterData = subTree[ daughterId ];

                newArborescenceLevelHtml    +=      '<div class="arborescence-level__witch ';
                if( daughterId === current ){
                    newArborescenceLevelHtml    +=              'current ';
                }
                
                newArborescenceLevelHtml    +=              '" data-id="' + daughterId + '" ';                
                newArborescenceLevelHtml    +=              'data-craft="' + daughterData['craft'] + '" ';
                newArborescenceLevelHtml    +=              'data-invoke="' + daughterData['invoke'] + '" >';
                
                if( daughterData['craft'] && daughterData['invoke'] ){
                    newArborescenceLevelHtml    +=      '<i  class="fas fa-hat-wizard" ';
                    newArborescenceLevelHtml    +=          'title="witch can craft & invoke"></i>';
                } else if( daughterData['craft'] ){
                    newArborescenceLevelHtml    +=      '<i  class="fas fa-mortar-pestle" ';
                    newArborescenceLevelHtml    +=          'title="witch can craft"></i>';                 
                } else if( daughterData['invoke'] ){    
                    newArborescenceLevelHtml    +=      '<i  class="fas fa-hand-sparkles" ';
                    newArborescenceLevelHtml    +=          'title="witch can invoke"></i>';
                } else {
                    newArborescenceLevelHtml    +=      '<i class="fa fa-folder" ';
                    newArborescenceLevelHtml    +=          'title="witch"></i>';
                }

                newArborescenceLevelHtml    +=          '<a class="arborescence-level__witch__name" ';
                if( daughterData['href'] !== undefined ){
                    newArborescenceLevelHtml+=              'href="' + daughterData['href'] + '" ';
                }
                newArborescenceLevelHtml    +=              'title="' + daughterData['description'] + '">';
                newArborescenceLevelHtml    +=              daughterData['name'];                
                newArborescenceLevelHtml    +=          '</a>';
                newArborescenceLevelHtml    +=          '&nbsp;&nbsp;';
                
                if( Object.keys(daughterData['daughters']).length > 0 ){
                    newArborescenceLevelHtml    +=          '<span class="arborescence-level__witch__daughters-display">';
                    newArborescenceLevelHtml    +=              '<i class="fas fa-chevron-down" title="Display daughters"></i>';
                    newArborescenceLevelHtml    +=          '</span>';
                }

                newArborescenceLevelHtml    +=      '</div>';
            });
            newArborescenceLevelHtml        +=  '</div>';
            newArborescenceLevelHtml        +=  '<div class="clear"></div>';

            $('#' + this.key + '.arborescence-menu-container .clear').remove();

            $('#' + this.key + '.arborescence-menu-container').append(newArborescenceLevelHtml);

            $('#' + this.key + '.arborescence-menu-container.module').animate({
                scrollLeft: $('#' + this.key + ' .arborescence-level').last().offset().left
            }, 700);

            return;
        }, 
        toggle: function( target )
        {
            if( target.currentTarget !== undefined ){
                target = target.currentTarget;
            }
            
            if( target.length === 0 ){
                return;
            }

            let container   = $(target).parents('.arborescence-menu-container');
            let expand      = !$(target).parents('.arborescence-level__witch').hasClass('selected');

            $(target).parents('.arborescence-level').nextAll().remove();
            $(target).parents('.arborescence-level').find('.arborescence-level__witch.selected').removeClass('selected');
            $(target).parents('.arborescence-level').find('.fa-chevron-right').removeClass('fa-chevron-right').addClass('fa-chevron-down');

            if( expand )
            {
                let witchId = $(target).parents('.arborescence-level__witch').data('id');
                let order   = this.treeData['daughters_orders'];
                let subTree = this.treeData;
                
                $(container).find('.arborescence-level .arborescence-level__witch.selected').each(function(index, element)
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
                
                $(target).parents('.arborescence-level__witch').addClass('selected');
                $(target).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-right');

                this.addArborescenceLevel( subTree , order );
            }
        }
    };
};

$(document).ready(function()
{
    var arborescenceMenuArray = [];
    
    for( let [key, data] of Object.entries(arborescencesInputs) ) 
    {
        arborescenceMenuArray[ key ] = ArborescenceMenu( key );
        arborescenceMenuArray[ key ].init( data ); 
        
        $('#'+key+'.arborescence-menu-container').on('click', '.arborescence-level__witch__daughters-display', function(e){
            arborescenceMenuArray[ key ].toggle( e );
        });         
    }
});
