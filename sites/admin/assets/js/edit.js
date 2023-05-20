
function confirmDiscard( date, autor )
{
    var message = "Voulez-vous vraiment supprimer définitivement ce brouillon ?";
    message += "\n"+"Créé " + date;
    message += "\n"+"Par " + autor;
    
    return confirm( message );
}

function quit()
{
    document.getElementById('actionsEdit').style.display = 'none';
    document.getElementById('saveOnQuit').style.display = 'block';
}

function cancelQuit()
{
    document.getElementById('saveOnQuit').style.display = 'none';
    document.getElementById('actionsEdit').style.display = 'block';
}
