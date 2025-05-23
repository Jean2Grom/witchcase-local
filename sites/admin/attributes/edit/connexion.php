<?php /** @var WC\Attribute\ConnexionAttribute $this */ 

use WC\User\Profile;

$profiles = Profile::listProfiles( $this->wc );

$sitesProfiles = [];
foreach( $profiles as $profileId => $profileItem )
{
    $siteLabel =  $profileItem->site;
    if( $siteLabel == '*' ){
        $siteLabel =  "Tous les sites";
    }
    
    if( empty($sitesProfiles[ $siteLabel ]) ){
        $sitesProfiles[ $siteLabel ] = [];
    }
    
    $sitesProfiles[ $siteLabel ][ $profileId ] = $profileItem;
}

include $this->wc->website->getFilePath( self::VIEW_DIR."/edit/connexion.php");