const ArborescenceMenu = function( key ){ 
    return {
        key: key,
        treeData: null,
        currentId: null,
        currentSite: null,
        breadcrumb: null,
        initPath: false,
        
        init: async function( entries )
        {
            this.treeData       = entries.treeData;
            this.currentId      = entries.currentId;
            this.currentSite    = entries.currentSite;
            this.breadcrumb     = entries.breadcrumb;            
            
            this.addArborescenceLevel( this.treeData )
            .then( 
                this.open( this.initPath )
            )
            .then(
                this.scrollToLastLevel()
            );

            return true;            
        },
        addArborescenceLevel: async function( subTree, order=false )
        {
            if( !order ){
                order = Object.keys(subTree);
            }

            let arborescenceLevelDom = document.createElement('div');
            arborescenceLevelDom.classList.add("arborescence-level");

            let current = this.currentId;

            order.forEach(  
                daughterId => {
                    let daughterData = subTree[ daughterId ];
//console.log(daughterData);
                    let arborescenceLevelWitchDom = document.createElement('div');
                    arborescenceLevelWitchDom.classList.add("arborescence-level__witch");
    
                    if( daughterId === current ){
                        arborescenceLevelWitchDom.classList.add("current");
                    }
                    
                    arborescenceLevelWitchDom.dataset.id        = daughterId;
arborescenceLevelWitchDom.dataset.craft     = daughterData['craft'];
                    arborescenceLevelWitchDom.dataset.cauldron  = daughterData['cauldron'];
                    arborescenceLevelWitchDom.dataset.invoke    = daughterData['invoke'];
                    
                    let iDom = document.createElement('i');
                    iDom.classList.add("fas");
                    //iDom.classList.add("fa");
    
                    if( daughterData['craft'] && daughterData['invoke'] ){
                        iDom.classList.add("fa-hat-wizard");
                    } else if( daughterData['craft'] ){
                        iDom.classList.add("fa-mortar-pestle");
                    } else if( daughterData['invoke'] ){
                        iDom.classList.add("fa-hand-sparkles");
                    } else {
                        iDom.classList.add("fa-folder");
                    }
                    arborescenceLevelWitchDom.append(iDom);
    
                    let aDom = document.createElement('a');
                    aDom.classList.add("arborescence-level__witch__name");
    
                    if( daughterData['href'] !== undefined ){
                        aDom.setAttribute('href', daughterData['href']);
                    }
                    aDom.setAttribute('title', daughterData['description']);
                    aDom.innerHTML = daughterData['name'];
                    arborescenceLevelWitchDom.append(aDom);
                    
                    if( Object.keys(daughterData['daughters']).length > 0 )
                    {
                        let spanDom = document.createElement('span');
                        spanDom.classList.add("arborescence-level__witch__daughters-display");
    
                        let iChevronDom = document.createElement('i');
                        iChevronDom.classList.add("fas");
                        iChevronDom.classList.add("fa-chevron-down");
                        spanDom.append(iChevronDom);
                        arborescenceLevelWitchDom.append(spanDom);
    
                        spanDom.addEventListener( 'click', 
                            e => this.toggle( e.target )
                                    .then( this.scrollToLastLevel() )
                        );
                    }
    
                    arborescenceLevelDom.append(arborescenceLevelWitchDom);
                }
            );

            document.querySelector('#' + this.key + '.arborescence-menu-container').append( arborescenceLevelDom );

            return true;
        }, 
        open: async function( initPath )
        {
            if( initPath === undefined || !initPath ){
                initPath = this.breadcrumb;
            }

            initPath.forEach( 
                async pathWitchId => {
                    let daughterTriggerSelector =   '#' + this.key + '.arborescence-menu-container ';
                    daughterTriggerSelector     +=   '.arborescence-level ';
                    daughterTriggerSelector     +=  '.arborescence-level__witch[data-id="' + pathWitchId + '"] ';
                    daughterTriggerSelector     +=  '.arborescence-level__witch__daughters-display';
                    
                    let daughterTrigger = document.querySelector(daughterTriggerSelector);
                    if( daughterTrigger ){
                        await this.toggle( document.querySelector(daughterTriggerSelector) );
                    }
                }
            );

            return true;
        },
        scrollToLastLevel: async function()
        {
            let target = document.querySelector('#' + this.key + '.arborescence-menu-container.module');

            if( target && target.lastChild){
                target.lastChild.scrollIntoView();
                //target.lastChild.scrollIntoView({ behavior: "smooth", block: "center", inline: "center" });
            }
        },
        toggle: async function( target )
        {
            let container       = document.querySelector('#' + this.key + '.arborescence-menu-container');
            let currentLevel    = target.closest('.arborescence-level');
            let currentWitch    = target.closest('.arborescence-level__witch');

            let expand          = true;
            if( currentWitch && currentWitch.classList.contains('selected') ){
                expand = false;
            }

            let matchedLevel    = false;
            container.querySelectorAll('.arborescence-level').forEach(
                level => {
                    if( matchedLevel ){
                        level.remove();
                    }

                    if( currentLevel === level )
                    {
                        matchedLevel = true;
                        let selectedWitch = level.querySelector('.arborescence-level__witch.selected');
                        if( selectedWitch ){
                            selectedWitch.classList.remove('selected');
                        }
                        let chevron = level.querySelector('.fa-chevron-right');
                        if( chevron )
                        {
                            chevron.classList.remove('fa-chevron-right');
                            chevron.classList.add('fa-chevron-down');
                        }
                    }
                }
            );

            if( expand )
            {
                let witchId = currentWitch.dataset.id;
                let order   = this.treeData['daughters_orders'];
                let subTree = this.treeData;
                
                container.querySelectorAll('.arborescence-level .arborescence-level__witch.selected').forEach( 
                    element => {
                        let subTreeId = element.dataset.id;

                        if( subTreeId !== witchId )
                        {
                            order   = subTree[ subTreeId ]['daughters_orders'];
                            subTree = subTree[ subTreeId ]['daughters'];
                        }
                    }
                );
                currentWitch.classList.add('selected');

                order   = subTree[ witchId ]['daughters_orders'];
                subTree = subTree[ witchId ]['daughters'];
                
                let chevron = currentWitch.querySelector('.fa-chevron-down');
                if( chevron )
                {
                    chevron.classList.remove('fa-chevron-down');
                    chevron.classList.add('fa-chevron-right');
                }

                this.addArborescenceLevel( subTree , order );
            }

            return true;
        }

    };
};

document.addEventListener("DOMContentLoaded", () => {
    var arborescenceMenuArray = [];
    
    for( let [key, data] of Object.entries(arborescencesInputs) ) 
    {
        arborescenceMenuArray[ key ] = ArborescenceMenu( key );
        arborescenceMenuArray[ key ].init( data ); 
    }
});
