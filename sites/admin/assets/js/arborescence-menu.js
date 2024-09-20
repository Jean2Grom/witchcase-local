const ArborescenceMenu = function( key ){ 
    return {
        key: key,

        treeData: null,
        currentId: null,
        currentSite: null,
        breadcrumb: null,
        draggable: null,
        draggedId: null,

        init: async function( entries )
        {
            this.treeData       = entries.treeData;
            this.currentId      = entries.currentId;
            this.currentSite    = entries.currentSite;
            this.breadcrumb     = entries.breadcrumb;
            this.draggable      = entries.draggable ?? false;

//console.log(this.draggable);
            this.addArborescenceLevel( this.treeData )
            .then( 
                this.open( this.breadcrumb )
            )
            .then(
                this.scrollToLastLevel()
            )
            .then(
                () => {
                    if( !this.draggable ){
                        return;
                    }

                    let container   = document.querySelector('#' + this.key + '.arborescence-menu-container.module');
                    container.addEventListener("contextmenu", 
                        e => {
                            e.preventDefault();

                            console.log('X:',e.x);
                            console.log('Y:',e.y);

                            this.triggerContextual(e);
                        }
                    );

                    container.addEventListener("click", 
                        e => {
                            let clickedMenu = e.target.closest('.arborescence-menu-context-menu');

                            if( !clickedMenu ){
                                container.querySelector('.arborescence-menu-context-menu').remove();
                            }
                        }
                    );
                }
            );

            return this;            
        },
        triggerContextual: function( e )
        {
            let container   = document.querySelector('#' + this.key + '.arborescence-menu-container.module');
            let menu        = document.createElement('menu');

            menu.classList.add("arborescence-menu-context-menu");
            menu.style.left = e.x +'px';
            menu.style.top  = e.y +'px';

            container.append(menu);
            console.log( container );
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
    
                    if( Object.keys(this.treeData).includes(daughterId) ){
                        //iDom.classList.add("fa-dungeon");
                        iDom.classList.add("fa-home");
                    }
                    else if( daughterData['cauldron'] && daughterData['invoke'] ){
                        iDom.classList.add("fa-hat-wizard");
                    } 
                    else if( daughterData['cauldron'] ){
                        iDom.classList.add("fa-mortar-pestle");
                    } 
                    else if( daughterData['invoke'] ){
                        iDom.classList.add("fa-hand-sparkles");
                    } 
                    else {
                        //iDom.classList.add("fa-skull");
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
                        //iChevronDom.classList.add("fas");
                        //iChevronDom.classList.add("fa-chevron-down");
                        iChevronDom.classList.add("far");
                        iChevronDom.classList.add("fa-folder");
                        spanDom.append(iChevronDom);
                        arborescenceLevelWitchDom.append(spanDom);
    
                        spanDom.addEventListener( 'click', 
                            e => this.toggle( e.target )
                                    .then( this.scrollToLastLevel() )
                        );
                    }

                    if( this.draggable && !Object.keys(this.treeData).includes(daughterId) )
                    {
                        arborescenceLevelWitchDom.style.cursor = 'grab';
                        arborescenceLevelWitchDom.setAttribute('draggable', true);

                        arborescenceLevelWitchDom.addEventListener( 'dragstart', 
                            e => {
                                //console.log( 'dragstart', e );
                                //arborescenceLevelWitchDom.style.cursor = 'grabbing';

                                this.draggedId = arborescenceLevelWitchDom.dataset.id;
                                console.log( 'dragstart2', this.draggedId );

                                //let openDom = arborescenceLevelWitchDom.querySelector('.fa-chevron-right');
                                let openDom = arborescenceLevelWitchDom.querySelector('.fa-folder-open');
                                if( openDom ){
                                    this.toggle( arborescenceLevelWitchDom.querySelector('.arborescence-level__witch__daughters-display') );
                                }
                            }
                        );
                        arborescenceLevelWitchDom.addEventListener( 'dragend', 
                            e => {
                                console.log( 'dragend', e.target);
                            }
                        );
                    }
                    
                    arborescenceLevelDom.append(arborescenceLevelWitchDom);

                }
            );

            if( this.draggable )
            {
                arborescenceLevelDom.addEventListener(
                    "dragenter",
                    e => {
                        e.preventDefault();

                        this.dragOver( e.target );
                    }
                );
                arborescenceLevelDom.addEventListener(
                    "dragover",
                    e => {
                        e.preventDefault();

                        this.dragOver( e.target );
                    }
                );
                arborescenceLevelDom.addEventListener(
                    "dragleave",
                    e => {
                        e.preventDefault();

                        this.dragleave( e.target );
                    }
                );
                arborescenceLevelDom.addEventListener(
                    "drop",
                    e => {
                        e.preventDefault();

                        this.dragleave( e.target );

                        console.log('drop X:',e.x);
                        console.log('drop Y:',e.y);

                        this.triggerContextual(e);
                    }
                );
            }

            document.querySelector('#' + this.key + '.arborescence-menu-container').append( arborescenceLevelDom );

            return true;
        }, 
        dragOver: function( target )
        {
            let witch       = target.closest('.arborescence-level__witch');
            let position    = target.closest('.arborescence-level__position');

            //console.log(witch.classList);
            if( witch && witch.dataset.id !== (this.draggedId ?? 0) )
            {
                if( !witch.classList.contains('drag-over') )
                {
                    witch.classList.add('drag-over');
                    let openDom = witch.querySelector('.arborescence-level__witch__daughters-display');
                    if( openDom && !witch.classList.contains('selected') ){
                        this.toggle( witch.querySelector('.arborescence-level__witch__daughters-display') );
                    }

                    document.querySelectorAll('#' + this.key + '.arborescence-menu-container .arborescence-level__position').forEach(
                        positionDom => positionDom.remove()
                    );

                    if( !witch.previousSibling || this.draggedId !== witch.previousSibling.dataset.id )
                    {
                        let positionTop = document.createElement('div');
                        positionTop.classList.add('arborescence-level__position');
                        positionTop.setAttribute('rel', 'before');
                        positionTop.setAttribute('ref', witch.dataset.id);
                        target.closest('.arborescence-level').insertBefore(  positionTop, witch );  
                    }
                    
                    if( !witch.nextSibling || this.draggedId !== witch.nextSibling.dataset.id )
                    {
                        let positionBottom = document.createElement('div');
                        positionBottom.classList.add('arborescence-level__position');
                        positionBottom.setAttribute('rel', 'after');
                        positionBottom.setAttribute('ref', witch.dataset.id);
                        witch.after(positionBottom);
                    }
                }
            }
            else if( position && !position.classList.contains('drag-over') )
            {
                position.classList.add('drag-over');

                document.querySelectorAll('#' + this.key + '.arborescence-menu-container .arborescence-level__position').forEach(
                    positionDom => {
                        if( positionDom !== position ){
                            positionDom.remove();
                        }
                    }
                );
        }

        },
        dragleave: function( target )
        {
            let witch = target.closest('.arborescence-level__witch');
            if( witch ){
                witch.classList.remove('drag-over');
            }

        },

        open: async function( path )
        {
            path.forEach( 
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

            if( target && target.lastChild ){
                //target.lastChild.scrollIntoView();
                target.lastChild.scrollIntoView({ behavior: "smooth", block: "center", inline: "end" });
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
                        //let chevron = level.querySelector('.fa-chevron-right');
                        let chevron = level.querySelector('.fa-folder-open');
                        if( chevron )
                        {
                            //chevron.classList.remove('fa-chevron-right');
                            //chevron.classList.add('fa-chevron-down');
                            chevron.classList.remove('fa-folder-open');
                            chevron.classList.add('fa-folder');
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
                
                //let chevron = currentWitch.querySelector('.fa-chevron-down');
                let chevron = currentWitch.querySelector('.far.fa-folder');
                if( chevron )
                {
                    //chevron.classList.remove('fa-chevron-down');
                    //chevron.classList.add('fa-chevron-right');
                    chevron.classList.remove('fa-folder');
                    chevron.classList.add('fa-folder-open');
                }

                this.addArborescenceLevel( subTree , order );
            }

            return true;
        }

    };
};

document.addEventListener("DOMContentLoaded", () => {
    var arborescenceMenuArray = [];
    
    for( let [key, data] of Object.entries(arborescencesInputs) ){
        arborescenceMenuArray[ key ] = (ArborescenceMenu( key )).init( data );
    }
});
