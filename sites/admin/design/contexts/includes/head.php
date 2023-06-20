<meta charset="utf-8">
<title><?=$title ?? "WitchCase Admin" ?></title>

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
            cursor: pointer;
        }
            body a:hover {
                /*color: #999999;*/
                color: #ff9900;
            }
    .clear {
        clear: both;
    }
    .text-right {
        text-align: right;
    }
    .text-left {
        text-align: left;
    }
</style>

