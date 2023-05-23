$(document).ready(function()
{
    var tabs = document.getElementsByClassName("tabs__item");
    
    var selectTab = function( event ){
        event.preventDefault();

        if( this.className.includes("selected") ){
            return false;
        }

        let seletedTabs = document.getElementsByClassName("tabs__item selected");
        for( let i = 0; i < seletedTabs.length; i++ ){
            seletedTabs[i].classList.remove("selected");
        }

        let seletedTargets = document.getElementsByClassName("tabs-target__item selected");
        for( let i = 0; i < seletedTargets.length; i++ ){
            seletedTargets[i].classList.remove("selected");
        }

        this.classList.add("selected");

        let targetId    = this.getAttribute("href").substring(1);
        let target      = document.getElementById( targetId );

        target.classList.add("selected");

        return false;
    };

    for( let i = 0; i < tabs.length; i++ ){
        tabs[i].addEventListener( 'click', selectTab, {passive: false} );
    }

    if( window.top.location.hash !== undefined && window.top.location.hash !== '' )
    {
        let tabFired = document.querySelectorAll( "[href='"+window.top.location.hash+"']" );
        
        let evObj = document.createEvent('Events');
        evObj.initEvent('click', true, false);
        tabFired[0].dispatchEvent(evObj);
        console.log(tabFired[0]);
    }
});