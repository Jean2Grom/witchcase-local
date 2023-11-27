<?php /** @var WC\Module $this */ 

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
