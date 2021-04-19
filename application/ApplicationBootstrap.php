<?php

namespace Application;

use Core\Bootstrap;
use Exception;

class ApplicationBootstrap extends Bootstrap
{
    /**
     * @return $this|ApplicationRouter
     * @throws Exception
     */
    protected function init(): self{


        return $this;
    }
}