$(document).ready(function()
{
    var tabs = document.getElementsByClassName("tabs__item");
    
    var selectTab = function( event ){
        event.preventDefault();
        
        if( this.parentNode.className.includes("selected") ){
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
        
        this.parentNode.classList.add("selected");
        
console.log(this);
        let targetId    = this.getAttribute("href").substring(1);
console.log(targetId);
        let target      = document.getElementById( targetId );
console.log(target);        
        target.classList.add("selected");
        
        return false;
    };

    for( let i = 0; i < tabs.length; i++ ){
        //tabs[i].addEventListener( 'click', selectTab, {passive: false} );
        for( let anchor of tabs[i].children ){
            anchor.addEventListener( 'click', selectTab, {passive: false} );
        }
    }

    window.triggerTabItem = function ( hash )
    {
        let tabFired    = document.querySelectorAll(".tabs__item a[href='" + hash + "']");
        let evObj       = document.createEvent('Events');
        
        evObj.initEvent('click', true, false);
        tabFired[0].dispatchEvent(evObj);
        return;
    };
    
    if( window.top.location.hash !== undefined && window.top.location.hash !== '' ){
        triggerTabItem( window.top.location.hash );
    }
    
    $('.tabs__item__triggering').click(function(){
        triggerTabItem( $(this).attr('href') );
        return false;
    });    
});