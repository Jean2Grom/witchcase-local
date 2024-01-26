<?php  /** @var WC\Module $this */ 

use WC\Website;

$result = false;
if( filter_has_var(INPUT_POST, "bouton_formulaire") )
{
    $prenom     = filter_input(INPUT_POST, "prenom");
    $nom        = filter_input(INPUT_POST, "nom");
    $adresse    = filter_input(INPUT_POST, "email");
    $societe    = filter_input(INPUT_POST, "societe");
    $message    = filter_input(INPUT_POST, "question");
    
    $captcha    = $this->wc->request->param('captcha');
    
    $ctx = stream_context_create([
        'http'=> ['timeout' => 10]
    ]);

    $wcCaptchaUrl   =   (new Website( $this->wc, "admin" ))->getFullUrl('captcha');
    $wcCaptchaUrl   .=  "?get=".$this->wc->website->name;
    
    if( file_get_contents($wcCaptchaUrl, false, $ctx) !== $this->wc->request->param('captcha') ){
        $this->wc->user->session->write('captcha-error', "Erreur de Captcha".$wcCaptchaUrl);
    }
    else 
    {
        $subject    = "[".strtoupper($societe)."] ".strtoupper($nom)." ".ucfirst(strtolower($prenom));
        $header     = "From: \"".$subject."\"<".$adresse.">\n";

        $result = mail("jean.de.gromard@gmail.com", $subject, $message, $header);
    }
}

include $this->getDesignFile();