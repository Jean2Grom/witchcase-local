<?php /** @var WC\Attribute\ConnexionAttribute $this */ 

use WC\User\Profile;

$profiles       = [];
if( !empty($this->values['profiles']) ){
    $profiles = Profile::listProfiles( $this->wc, [ 'profile.id' => $this->values['profiles'] ] );
}

include $this->wc->website->getFilePath( self::VIEW_DIR."/view/connexion.php" );