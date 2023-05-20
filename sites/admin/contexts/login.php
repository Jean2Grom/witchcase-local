<?php
$faviconFile    = substr( $this->getImageFile("favicon.ico"), 1);
$faviconMime    = mime_content_type($faviconFile) ?? '';
$faviconContent = base64_encode( file_get_contents($faviconFile) ) ?? '';

include $this->getDesignFile();