<?php

namespace Configs;

use Application\ApplicationContainer;

abstract class ContainerConfigs {
    public const BOOTSTRAP_CONTAINER_CLASS = ApplicationContainer::class;
}