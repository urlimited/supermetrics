<?php

namespace Application\Controllers;

use Application\ApplicationContainer;
use Application\Models\StatsModel;
use Core\Controller;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Memcached;

class ApiController extends Controller
{


    public function getStatisticsOnPosts()
    {
        try {
            $stats = new StatsModel();

            return var_dump(ApplicationContainer::getInstance()->cacheDriver->get('statistics_key'));

            //return var_dump($stats->getStats());
        } catch (Exception $e) {
            return $e->getMessage();
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }
}