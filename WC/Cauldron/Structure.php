<?php
namespace WC\Cauldron;

use WC\WitchCase;

class Structure 
{
    public ?string $file;
    public ?string $name;

    public array $properties    = [];

    var ?array $composition;

    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;



}