<div class="wrapper row3">
    <footer id="footer" class="clear">
        <p class="fl_left">
            <a  href="https://www.witchcase.com" 
                target="_blank">
                WitchCase
            </a> 
            Copyright&copy;2021 - All Rights Reserved
        </p>
    </footer>
    
    <?php foreach( $this->getJsFiles() as $jsFile ): ?>
        <script src="<?=$jsFile?>"></script>
    <?php endforeach; ?>
</div>