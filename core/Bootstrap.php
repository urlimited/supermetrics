<?php

namespace Core;

use Runnable;

abstract class Bootstrap extends Singleton implements Runnable {

    abstract public function init(): self;

    abstract public function run(): self;
}