<?php

declare(strict_types=1);

namespace Hcode;

use Rain\Tpl;

class Page
{
    private $tpl;
    private $options = [];
    private $defaults = [
      'header' => true,
      'footer' => true,
      'data' => [],
    ];

    /**
     * __construct.
     *
     * @param mixed $opts
     * @param mixed $tpl_dir
     */
    public function __construct($opts = [], $tpl_dir = '/views/')
    {
        $this->options = array_merge($this->defaults, $opts);
        // config
        $config = [
                    'tpl_dir' => $_SERVER['DOCUMENT_ROOT'].$tpl_dir,
                    'cache_dir' => $_SERVER['DOCUMENT_ROOT'].'/views-cache/',
                    'debug' => false,
                   ];

        Tpl::configure($config);

        $this->tpl = new Tpl();

        $this->setData($this->options['data']);

        if (true === $this->options['header']) {
            $this->tpl->draw('header');
        }
    }

    /**
     * __destruct.
     */
    public function __destruct()
    {
        if (true === $this->options['footer']) {
            $this->tpl->draw('footer');
        }
    }

    /**
     * setTpl.
     *
     * @param mixed $name
     * @param mixed $data
     * @param mixed $returnHTML
     */
    public function setTpl($name, $data = [], $returnHTML = false)
    {
        $this->setData($data);

        return $this->tpl->draw($name, $returnHTML);
    }

    /**
     * setData.
     *
     * @param mixed $data
     */
    private function setData($data = [])
    {
        foreach ($data as $key => $value) {
            $this->tpl->assign($key, $value);
        }
    }
}