function  generateWcCaptcha()
{
    fetch( wcCaptchaUrl )
    .then( (response) => {
        if( !response.ok ){
            throw new Error(`HTTP error, status = ${response.status}`);
        }

        return response.text();
    })
    .then( (WcCaptchaHTML) => {
        document.querySelector("#"+wcCaptchaId).innerHTML = WcCaptchaHTML;

        document.querySelector("#"+wcCaptchaId+' .wc-captcha-refresh')
            .addEventListener("click", (e) => {
                e.preventDefault();
                generateWcCaptcha();
                return false;
            });        
    })
    .catch((error) => {
        document.querySelector("#"+wcCaptchaId).innerHTML = "<p>Captcha loading failure...</p>";
    });
}

generateWcCaptcha();
