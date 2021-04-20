<?php

namespace Core;

use Exception;

abstract class Singleton
{
    protected function __construct()
    {
    }

    final private function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

    final public static function getInstance()
    {
        static $instances = array();

        $calledClass = get_called_class();

        if (!isset($instances[$calledClass]))
            $instances[$calledClass] = new $calledClass();

        return $instances[$calledClass];
    }
}