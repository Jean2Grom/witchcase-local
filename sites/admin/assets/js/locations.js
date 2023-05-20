
function change_onglet( sectionId, ongletId )
{
    document.getElementById('onglet_' + sectionId + '_' + anc_onglet[sectionId]).className = 'onglet_0 onglet';
    document.getElementById('onglet_' + sectionId + '_' + ongletId).className = 'onglet_1 onglet';
    document.getElementById('contenu_' + sectionId + '_' + anc_onglet[sectionId]).style.display = 'none';
    document.getElementById('contenu_' + sectionId + '_' + ongletId).style.display = 'block';
    anc_onglet[sectionId] = ongletId;
}

for( i=0; i<init_section.length; i++ )
{
    change_onglet( init_section[i], init_onglet[i] );
}