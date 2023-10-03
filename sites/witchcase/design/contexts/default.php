<!DOCTYPE html>
<html class="wf-opensans-n3-active wf-opensans-n7-active wf-opensans-n4-active wf-active" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.25, maximum-scale=5.0, target-densitydpi=device-dpi" />
    <meta name="description" content="<?=$contextCraft->attribute('meta-description')->content()?>">
    <meta name="keywords" content="<?=$contextCraft->attribute('meta-keywords')->content()?>">
    <meta charset="utf-8" />

    <title><?=$contextCraft->attribute('meta-title')->content()?></title>    
    <?=$this->favicon()?>
    
    <?=$this->js('jquery.min.js');?>
    <?php foreach( $this->getJsLibFiles() as $jsLibFile ): ?>
        <script src="<?=$jsLibFile?>"></script>
    <?php endforeach; ?>
    
    <?=$this->css('styles.css');?>
    <?=$this->css('responsive.css',  [ "media" => "screen and (max-width: 800px)" ]);?>
    
    <?php foreach( $this->getCssFiles() as $cssFile ): ?>
        <link   rel="stylesheet" 
                href="<?=$cssFile?>" />
    <?php endforeach; ?>
</head>
<body>    
    <div id="page">
        <div class="socialbar">
            <div class="content_socialbar">
                <?=$this->image('lettre_mail.jpg')?>
                <?php $contextCraft->attribute('contact-email')->display(); ?>
            </div>
        </div>
        
        <div class="menu">
            <a href="<?=$this->wc->website->getUrl() ?>">
                <?php $contextCraft->attribute('logo')->display(); ?>
            </a>
            
            <div id="download" >
                <?php $contextCraft->attribute('call-to-action')->display(); ?>
            </div>
            
            <div class="content_menu">  
                <ul>
                    <?php foreach( $this->wc->witch('home')->daughters() as $menuItem ): ?>
                        <li>
                            <a  <?=( $menuItem === $this->wc->witch() )? 'id="en_cours"': ''?>
                                href="<?=$menuItem->getUrl() ?>">
                                <?=$menuItem->name ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul> 
            </div>
        </div>
        
        <div id="conteneur">
            <div    id="contenu_index" 
                    style="background:url(<?=$this->getVar('backgroundImage') ?? $contextCraft->attribute('background')->content('file')?>) no-repeat center center;">
                <h1><?=$this->getVar('headlineText') ?? $contextCraft->attribute('headline')->content()?></h1>
                <div id="baseline"><?=$this->getVar('bodyText') ?? $contextCraft->attribute('body')->content()?></div>
                
                <div id="boite_bouton">
                    <div id="bouton">
                        <?php $contextCraft->attribute('call-to-action')->display(); ?>
                    </div>
                </div>
            </div>
            
            <?=$this->wc->cairn->invokation() ?>
        </div>
        
        <div id="footer">
            <div id="footer_content">
                <div id="copyright">
                    ©WitchCase2023. All Right Reserved
                </div>
            </div>
        </div>
    </div>
    
    <?php foreach( $this->getJsFiles() as $jsLibFile ): ?>
        <script src="<?=$jsLibFile?>"></script>
    <?php endforeach; ?>
    
</body>
</html>
