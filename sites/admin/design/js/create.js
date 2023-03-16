
function testForm() {

    var errorMesage = '';

    /*if( document.querySelector('input[name="structure"]:checked') )
    {
        var testType = document.querySelector('input[name="structure"]:checked').value;
    }

    if( undefined == testType )
    {
        errorMesage += 'Vous devez séléctionner un type de contenu' + "\n";
    }*/

    if( document.getElementById('name').value == '' )
    {
        errorMesage += 'Vous devez saisir un nom';
    }
    
    if( errorMesage != '' )
    {
        alert(errorMesage);
        return false;
    }
    else
    {
        document.getElementById('contentCreationForm').submit();
    }
}

