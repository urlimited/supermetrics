<?php

namespace Core;

use Exception;

abstract class Singleton
{
    protected static self $instance;

    protected function __construct(){}

    protected function __clone() { }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(){
        if(!isset(self::$instance))
            self::$instance = new static();

        return self::$instance;
    }
}