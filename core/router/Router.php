<?php

namespace Core\Router;

use Core\Singleton;
use Exception;
use Core\Runnable;

abstract class Router extends Singleton implements Runnable
{
    protected array $routes = [];

    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $method = array_filter($this->routes, function (Route $route) {
            return $route->isAppropriate($_SERVER['REQUEST_URI']);
        });

        if (empty($method))
            //TODO: realize special not found exception
            throw new Exception('');

        $method[0]->runMethodController();
    }


    /**
     * @param $path
     * @param $controller
     * @param $method
     * @param string $name
     * @return $this
     * @throws Exception
     */
    protected function registerRoute($path, $controller, $method, $name = ''): self
    {
        $this->routes[] = new Route(['path' => $path, 'controller' => $controller, 'method' => $method, 'name' => $name]);

        return $this;
    }
}