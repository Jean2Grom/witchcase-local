<?php  /** @var WC\Module $this */ 

$result = false;
if( filter_has_var(INPUT_POST, "bouton_formulaire") )
{
    $prenom     = filter_input(INPUT_POST, "prenom");
    $nom        = filter_input(INPUT_POST, "nom");
    $adresse    = filter_input(INPUT_POST, "email");
    $societe    = filter_input(INPUT_POST, "societe");
    $message    = filter_input(INPUT_POST, "question");
    
    if( $this->wc->user->session->read('captcha') !== $this->wc->request->param('captcha') ){
        $this->wc->user->session->write('captcha-error', "Erreur de Captcha");
    }
    else 
    {
        $subject    = "[".strtoupper($societe)."] ".strtoupper($nom)." ".ucfirst(strtolower($prenom));
        $header     = "From: \"".$subject."\"<".$adresse.">\n";

        $result = mail("jean.de.gromard@gmail.com", $subject, $message, $header);
    }
}

include $this->getDesignFile();