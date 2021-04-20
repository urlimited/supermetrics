<?php

namespace Application\Controllers;

use Application\Models\StatsModel;
use Core\Controller;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

class ApiController extends Controller
{
    public function getStatisticsOnPosts()
    {
        try {
            $stats = new StatsModel();

            return json_encode(['status' => 200, 'body' => $stats->getStats()]);
        } catch (Exception $e) {
            return json_encode(['status' => 500, 'body' => $e->getMessage()]);
        } catch (GuzzleException $e) {
            return json_encode(['status' => 422, 'body' => $e->getMessage()]);
        }
    }

    public function statisticsClearCache(){
        try {
            $stats = new StatsModel();

            $stats->update();

            return json_encode(['status' => 200, 'body' => 'cache is cleared']);
        } catch (GuzzleException $e) {
            return json_encode(['status' => 422, 'body' => $e->getMessage()]);
        } catch (Exception $e) {
            return json_encode(['status' => 500, 'body' => $e->getMessage()]);
        }
    }
}