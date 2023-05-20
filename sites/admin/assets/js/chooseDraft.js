
function testSelection() {
    if( document.querySelector('input[name="editDraftID"]:checked') )
    {
        var test = document.querySelector('input[name="editDraftID"]:checked').value;
    }

    if( !test )
    {
        alert('Vous devez séléctionner un brouillon ou en créer un');
        return false;
    }
    else
    {
        document.getElementById('chooseDraft').submit();
    }
}
