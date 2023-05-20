
function limitationChange()
{
    var selectedIndex = document.getElementById("limitation").selectedIndex;

    if( selectedIndex == '2' )
    {
        document.getElementById('limitation_profile').style.display = "block";
    }
    else
    {
        document.getElementById('limitation_profile').style.display = "none";
    }
}

