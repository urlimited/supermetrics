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
    public function init(): self{


        return $this;
    }

    function run(): void
    {
        // TODO: Implement run() method.
    }
}