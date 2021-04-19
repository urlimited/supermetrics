<?php

namespace Core\Router;

use Exception;

class Route {
    protected string $path;

    protected string $name;

    protected string $controller;

    protected string $method;

    /**
     * Route constructor.
     * @param $data
     * @throws Exception
     */
    public function __construct($data){
        $this->validate($data);

        $this->path = $data['path'];
        $this->name = $data['name'];
    }

    public function getRouteName(): string
    {
        return $this->name;
    }

    public function getRoutePath(): string
    {
        return $this->path;
    }

    /**
     * @param $data
     * @throws Exception
     */
    private function validate($data){
        if(!array_key_exists('path', $data) || !array_key_exists('name', $data))
            throw new Exception('Data that is provided to Route is incorrect');
    }
}