<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include $this->getIncludeDesignFile('head.php'); ?>
    </head>
    
    <body>
        <?php include $this->getIncludeDesignFile('header.php'); ?>
        
        <?=$this->wc->witch()->result ?>
        
        <!-- footer -->
        <?php include $this->getIncludeDesignFile('footer.php'); ?>
    </body>
</html>