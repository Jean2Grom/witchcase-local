<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>WitchCase</title>
        
        <?php if( isset($faviconMime) && isset($faviconContent) ): ?>
            <link rel="icon" type="<?=$faviconMime?>" href="data:<?=$faviconMime?>; base64,<?=$faviconContent?>" />
        <?php endif; ?>
        
        <?php foreach( $this->getJsLibFiles() as $jsLibFile ): ?>
            <script src="<?=$jsLibFile?>"></script>
        <?php endforeach; ?>
        
        <?php foreach( $this->getCssFiles() as $cssFile ): ?>
            <link   rel="stylesheet" 
                    type="text/css" 
                    href="<?=$cssFile?>" />
        <?php endforeach; ?>
        
        <style>
            body {
                font-family: Helvetica;
                color: #424242;
                margin: 0;
                background-color: #eee;
            }
                body a {
                    color: #424242;
                    font-weight: bold;
                    text-decoration: none;
                }
                    body a:hover {
                        color: #ff9900;
                    }
        </style>        
    </head>
    
    <body>        
        <?=$this->wc->witch()->result ?>
    </body>
</html>