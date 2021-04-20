<?php

namespace Application;

use Core\Container;
use Exception;
use Memcached;

class ApplicationContainer extends Container
{
    public Memcached $cacheDriver;

    /**
     * @return $this|ApplicationRouter
     * @throws Exception
     */
    public function init(): self{
        // TODO: dependency injection should be here
        $this->cacheDriver = new Memcached();
        $this->cacheDriver->addServer('127.0.0.1', 11211);

        return $this;
    }

    function run(): void
    {
        // TODO: Implement run() method.
    }
}