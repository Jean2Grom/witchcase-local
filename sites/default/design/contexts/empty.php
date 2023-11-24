<?php /** @var WC\Context $this */ ?>

<?php foreach( $this->getJsLibFiles() as $jsLibFile ): ?>
    <script src="<?=$jsLibFile?>"></script>
<?php endforeach; ?>

<?php foreach( $this->getCssFiles() as $cssFile ): ?>
    <link   rel="stylesheet" 
            type="text/css" 
            href="<?=$cssFile?>" />
<?php endforeach; ?>

<?=$this->wc->witch()->result() ?>