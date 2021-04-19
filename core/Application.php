<?php

namespace Application\Core;

use BootstrapConfigs;
use Core\Bootstrap;
use Core\Router\Router;
use Core\Singleton;
use RouterConfigs;
use Runnable;

class Application extends Singleton implements Runnable
{

    protected Router $router;

    protected Bootstrap $bootstrap;

    public function init(): self
    {
        $this->bootstrap = (call_user_func(RouterConfigs::ROUTER_CLASS . '::getInstance'))->init();
        $this->router = (call_user_func(BootstrapConfigs::BOOTSTRAP_CLASS . '::getInstance'))->init();

        return $this;
    }

    public function run(): self
    {
        $this->bootstrap->run();
        $this->router->run();
    }
}