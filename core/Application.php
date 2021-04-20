<?php

namespace Core;

use Configs\BootstrapConfigs;
use Core\Router\Router;
use Configs\RouterConfigs;

class Application extends Singleton implements Runnable
{

    protected Router $router;

    protected Bootstrap $bootstrap;

    public function init(): self
    {
        $this->router = (call_user_func(RouterConfigs::ROUTER_CLASS . '::getInstance'))->init();
        $this->bootstrap = (call_user_func(BootstrapConfigs::BOOTSTRAP_CLASS . '::getInstance'))->init();

        return $this;
    }

    public function run(): void
    {
        $this->bootstrap->run();
        $this->router->run();
    }
}