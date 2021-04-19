<?php

use Application\ApplicationBootstrap;
use Application\ApplicationRouter;

abstract class RouterConfigs {
    public const ROUTER_CLASS = ApplicationRouter::class;
}

abstract class BootstrapConfigs {
    public const BOOTSTRAP_CLASS = ApplicationBootstrap::class;
}