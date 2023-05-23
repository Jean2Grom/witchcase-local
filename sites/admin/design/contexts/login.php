<?php 
    $this->addCssFile('header.css');
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include $this->getIncludeDesignFile('head.php'); ?>
        <style>
            footer {
                text-align: center;
            }
        </style>
    </head>
    
    <body>
        <!-- header -->
        <?php include $this->getIncludeDesignFile('header.php'); ?>
        
        <?=$this->wc->witch()->result ?>
        
        <!-- footer -->
        <?php include $this->getIncludeDesignFile('footer.php'); ?>
    </body>
</html>