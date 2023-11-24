<?php /** @var WC\Module $this */

const WC_CAPTCHA_ITERATIONS = 3;

$id             = "wc-captcha-".md5(rand());   
$match          = rand(0, WC_CAPTCHA_ITERATIONS - 1);
$captchaImages  = [];
$hintImage      = null;
for( $i=0; $i < WC_CAPTCHA_ITERATIONS; $i++ )
{
    $randomAlpha    = md5(rand());
    $captchaCode    = substr($randomAlpha, 0, 6);
    
    $red    = rand(0, 255);
    $green  = rand(0, 255);
    $blue   = rand(0, 255);
    
    $image      = imagecreatetruecolor(150, 35);
    $bg         = imagecolorallocate($image, (255 - $red), (255 - $green), (255 - $blue));
    
    imagefill($image, 0, 0, $bg);
    
    $color = imagecolorallocate($image, $red, $green, $blue);
    
    imagestring( $image, 5, 50, 10, $captchaCode, $color );
    imagegammacorrect($image, 1.0, 2.0);
    
    ob_start (); 
    imagepng( $image );
    $imageData = ob_get_contents (); 
    ob_end_clean (); 
    
    $captchaImages[ $i ] = $imageData;
    
    if( $i == $match )
    {
        $this->wc->user->session->write('captcha', $captchaCode);
        
        if( WC_CAPTCHA_ITERATIONS > 1 )
        {
            $string = "Code is the ".($match + 1);

            $image = imagecreatefrompng( substr($this->getImageFile('captcha-layout-6.png'), 1) );
            $color = imagecolorallocate($image, 0, 0, 0);

            $px     = (int) (imagesx($image) - 8 * strlen($string)) / 2;
            imagestring($image, 4, $px, 13, $string, $color);

            ob_start (); 
            imagepng($image);
            $hintImage = ob_get_contents (); 
            ob_end_clean (); 
        }
    }
}
$this->setContext('empty');
?>

<div class="wc-captcha-container" id="<?=$id?>">
    <div class="wc-captcha-col">
        <?php if( $hintImage ): ?>
            <img src="data:image/png;base64,<?=base64_encode( $hintImage )?>" /> 
        <?php endif; ?>
        <fieldset>
            <label for="input-<?=$id?>">write captcha code</label>
            <br/>
            <input type="text" 
                   id="input-<?=$id?>"
                   name="captcha" 
                   maxlength="6" 
                   size="6"/>
        </fieldset>
        <button class="wc-captcha-refresh">
            <i class="fa fa-refresh"></i>
            Refresh 
        </button>
    </div>
    <div class="wc-captcha-col">
        <?php foreach($captchaImages as $imageData): ?>
            <img class="wc-captcha" src="data:image/png;base64,<?=base64_encode( $imageData )?>" /> 
        <?php endforeach; ?>
    </div>
</div>

<style>
    .wc-captcha-container {
        display: flex;
        align-items: flex-start;
    }
    .wc-captcha-col {
        display: flex;
        flex-direction: column;
        margin-right: 8px;
        justify-content: space-between;
        align-items: center;
    }
    .wc-captcha-col fieldset {
        margin-bottom: 8px;
        width: 120px;
        text-align: center;
        border: 1px solid #414141;
        border-radius: 15px;
        font-size: 0.8em;
        font-weight: bold;
    }
        .wc-captcha-col fieldset input {
            margin-top: 4px;
            max-width: 55px;
        }
    .wc-captcha {
        border: 1px solid #424242;
        border-radius: 15px;
        box-shadow: 4px 4px 4px #999;
        opacity: 0.8;
        margin-bottom: 8px;
    }
</style>

<script>
    var wcCaptchaId = '<?=$id?>';
    
    $('#' + wcCaptchaId + " .wc-captcha-refresh").click( function(){
        
        let href = $(this).data('href');
        
        $.ajax( href )
            .done( function( data ) 
            {
                let target = $('#' + wcCaptchaId);
                $(target).after( data );
                $(target).remove();
                
                $('#' + wcCaptchaId + " .wc-captcha-refresh").data( 'href', href );
            });
            
        return false;    
    });    
</script>