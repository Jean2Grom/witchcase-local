<footer>
    Powered by
    <a  href="https://www.witchcase.com" 
        target="_blank">
        WitchCase
    </a> 
</footer>

<?php foreach( $this->getJsFiles() as $jsFile ): ?>
    <script src="<?=$jsFile?>"></script>
<?php endforeach; ?>