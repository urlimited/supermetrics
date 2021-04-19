<?php

namespace Core\Router;

use Core\Singleton;
use Exception;
use Runnable;

abstract class Router extends Singleton implements Runnable
{
    protected array $routes = [];

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getExecutableMethod(){
        //TODO: handle exception if not matched
        return array_filter($this->routes, function(Route $route){
            return $route->isAppropriate($_SERVER['REQUEST_URI']);
        });
    }

    abstract public function init(): self;

    abstract public function run(): self;

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