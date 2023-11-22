<!DOCTYPE html>
<?php /** @var WC\Context $this */

$this->addCssFile('base.css');
$this->addCssFile('basic.css');
$this->addCssFile('header-footer.css');
?>
<html lang="fr">
    <head>
        <?php include $this->getIncludeDesignFile('head.php'); ?>
    </head>
    
    <body>
        <div class="container">
            <header><?php include $this->getIncludeDesignFile('header.php'); ?></header>
            <main><?=$this->wc->witch()->result() ?></main>
            <footer><?php include $this->getIncludeDesignFile('footer.php'); ?></footer>
        </div>
        
        <?php foreach( $this->getJsFiles() as $jsFile ): ?>
            <script src="<?=$jsFile?>"></script>
        <?php endforeach; ?>        
    </body>
</html>