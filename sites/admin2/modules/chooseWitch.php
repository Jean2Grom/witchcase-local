<?php
use WC\Witch;

$currentId  = $this->wc->request->param("id", "get", FILTER_VALIDATE_INT) ?? $this->wc->witch()->id;
$root       = Witch::recursiveTree( $this->witch, $this->wc->website->sitesRestrictions, $currentId, $this->maxStatus );
$tree       = [ $this->witch->id => $root ];
$breadcrumb = [ $this->witch->id ];

$this->view();
