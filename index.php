<?php

use Application\Core\Application;

require "vendor/autoload.php";

(Application::getInstance())->init()->run();
