<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>WitchCase</title>

        <link rel="icon" type="<?=$faviconMime?>" href="data:<?=$faviconMime?>; base64,<?=$faviconContent?>" />

        <?php foreach( $this->getJsLibFiles() as $jsLibFile ): ?>
            <script src="<?=$jsLibFile?>"></script>
        <?php endforeach; ?>

        <?php foreach( $this->getCssFiles() as $cssFile ): ?>
            <link   rel="stylesheet" 
                    type="text/css" 
                    href="<?=$cssFile?>" />
        <?php endforeach; ?>
    </head>
    
    <body>
        <?=$this->website->witches["current"]->result ?>
    </body>
</html>