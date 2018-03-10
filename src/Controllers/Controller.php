<?php

namespace Jose\Controllers;

use Jose\App;

abstract class Controller
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }
}
