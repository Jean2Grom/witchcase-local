<?php
namespace WC\Cauldron;

use WC\WitchCase;

class Structure 
{
    const DEFAULT_TYPE = "structure";

    public ?string $file;
    public ?string $name;

    public string $type         = self::DEFAULT_TYPE;
    public array $properties    = [];

    var ?self $structure;
    var ?array $composition;

    /** 
     * WitchCase container class to allow whole access to Kernel
     * @var WitchCase
     */
    public WitchCase $wc;



}