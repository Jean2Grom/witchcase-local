<meta charset="utf-8">
<title>
    <?=$title ?? "WitchCase Admin" ?>
</title>

<link rel="icon" type="<?=$faviconMime?>" href="data:<?=$faviconMime?>; base64,<?=$faviconContent?>" />

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
                /*color: #999999;*/
                color: #ff9900;
            }
    .clear {
        clear: both;
    }
    .text-right {
        text-align: right;
    }
    header {
        position: sticky;
        height: 60px;
        top: 0;
        z-index: 10;
        padding: 7px 20px;
        text-align: center;
        box-shadow: 0px 3px 5px #cccccc;
        background-color: #FFFFFF;
    }
        header h1 {
            font-size: 1.5em;
        }
        .header__logo {
            position: absolute;
            left: 25px;
            top: 10px;
        }
            .header__logo img {
                height: 55px;
            }
        .header__user {
            position: absolute;
            right: 25px;
            top: 15px;
        }
            .header__user__label {
                font-size: 1.1em;
            }
            .header__user__content {
                margin-top: 15px;
            }
    nav {
        height: 100%;
        position: fixed;
        z-index: 7;
        background-color: #cccccc;
        overflow-x: hidden;
        width: 125px;
    }
        nav a {
            display: block;
            margin-top: 20px;
            margin-left: 20px;
        }
    section, footer {
        margin-left: 125px;
        padding: 20px;
    }
    section {
        padding-bottom: 0;
    }
    footer {
        margin-left: 0;
        text-align: center;
        
    }
    .breadcrumb {
        padding-bottom: 5px;
    }
    .tabs {
        margin-top: 15px;
    }
        .tabs__item {
            float: left;
            border-top: 1px solid #ccc;
            border-left: 1px solid #ccc;
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 8px 16px;
            margin-right: 5px;
            cursor: pointer;
            background-color: #f3f3f3;
            /*background-color: #FFF;*/
        }
            .tabs__item.selected {
                border-bottom: 1px solid #FFF;
                background-color: #FFF;
                color: #ff9900;
            }
    .tabs-target {
        border: 1px solid #ccc;
        padding-top: 5px;
        margin-top: -1px;
        box-shadow: 5px 5px 5px #ccc;
        background-color: #FFF;

    }
        .tabs-target__item {
            display: none;
            padding: 15px;
        }
            .tabs-target__item.selected {
                display: block;
            }
    .alert-message {
        border: 1px solid #424242;
        padding: 10px;
        display: block;
        width: max-content;
        margin-bottom: 15px;
        border-radius: 10px;  
        box-shadow: 3px 3px 5px #aaa;
        color: #424242;
        font-style: italic;
    }
        .alert-message.error {
            border-color: #dc3545;
            color: #dc3545;
        }
        .alert-message.warning {
            border-color: #ff9900;
            color: #ff9900;
        }
        .alert-message.success {
            border-color: #28a745;
            color: #28a745;
        }
        .alert-message strong {
            font-weight: bold;
        }    
</style>

