<?php
$contextCraft = $this->wc->witch('home')->craft();

$this->addJsFile('witch.js');
$this->addJsFile('case.jq.js');
$this->view();