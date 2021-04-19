<?php

namespace Application;

use Core\Router\Router;

$router = Router::getInstance();

$router->registerRoute('constructed', 'Controller', 'method');