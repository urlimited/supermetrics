<?php

namespace Core;

use Configs\ContainerConfigs;
use Core\Router\Router;
use Configs\RouterConfigs;
use Exception;

class Application extends Singleton implements Runnable
{

    protected Router $router;

    protected Container $bootstrap;

    public function init(): self
    {
        $this->router = (call_user_func(RouterConfigs::ROUTER_CLASS . '::getInstance'))->init();
        $this->bootstrap = (call_user_func(ContainerConfigs::BOOTSTRAP_CONTAINER_CLASS . '::getInstance'))->init();

        return $this;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->bootstrap->run();

        try {
            $this->router->run();
        } catch (Exception $e) {
            echo json_encode([
                'status' => 404,
                'body' => 'Page not found'
            ]);
        }

    }
}