<?php

namespace Application;

use Core\Router\Router;
use Exception;

class ApplicationRouter extends Router
{
    /**
     * @return $this|ApplicationRouter
     * @throws Exception
     */
    protected function init(): self{
        $this->registerRoute('/', 'controller', 'method');

        return $this;
    }
}