<?php

require 'vendor/autoload.php';

session_start();

$wc = new \WC\WitchCase();

$wc->injest()->run();