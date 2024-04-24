<?php /** @var WC\Module $this */

use WC\Handler\WitchHandler;

$currentId  = $this->witch("target")?->id ?? $this->witch()?->id;
$root       = WitchHandler::recursiveTree( $this->witch, $this->wc->website->sitesRestrictions, $currentId, $this->maxStatus );
$tree       = [ $this->witch->id => $root ];
$breadcrumb = [ $this->witch->id ];

$this->view();