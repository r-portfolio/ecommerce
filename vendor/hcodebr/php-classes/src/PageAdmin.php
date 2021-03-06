<?php

declare(strict_types=1);

namespace Hcode;

class PageAdmin extends Page
{
    /**
     * __construct.
     *
     * @param mixed $opts
     * @param mixed $tpl_dir
     */
    public function __construct($opts = [], $tpl_dir = '/views/admin/')
    {
        parent::__construct($opts, $tpl_dir);
    }
}