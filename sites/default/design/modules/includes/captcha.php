<?php /** @var WC\Module $this */ 

/**
 * - To add a captcha check into your form, you have to add this code :
 * 
 * include $this->getIncludeDesignFile('captcha.php');
 * 
 * - To check captcha validation in PHP file, use this test : 
 * 
 * $this->wc->user->session->read('captcha') === $this->wc->request->param('captcha')
 * 
 * - To display "Wrong Catpcha" or custom label :
 * 
 * $this->wc->user->session->write('captcha-error', true|"CUSTOM_LABEL");
 */

use WC\Website;

$this->addCssFile('captcha.css');
$this->addJsFile('captcha.js');

$id = "wc-captcha-container-".md5(rand());   
if( $this->wc->website->site !== "admin" ){
    $wcCaptchaUrl = $this->wc->website->getUrl('captcha');
}
else {
    $wcCaptchaUrl =  (new Website( $this->wc, "admin" ))->getUrl('captcha');
}
?>
<div class="wc-captcha-container" id="<?=$id?>">
    <div class="wc-captcha">
        <i class="fa fa-circle-notch fa-spin"></i>
    </div>
</div>
<script>
    var wcCaptchaUrl  = '<?=$wcCaptchaUrl ?>';
    var wcCaptchaId   = '<?=$id ?>';
</script>
