<?php

namespace Application;

use Application\Controllers\ApiController;
use Core\Router\Router;
use Exception;

class ApplicationRouter extends Router
{
    /**
     * @return $this|ApplicationRouter
     * @throws Exception
     */
    public function init(): self
    {
        $this->registerRoute('/', ApiController::class, 'getStatisticsOnPosts');
        $this->registerRoute('/update_cache', ApiController::class, 'statisticsClearCache');

        return $this;
    }
}