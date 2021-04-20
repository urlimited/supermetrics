<?php

namespace Application\Models;

use Application\ApplicationContainer;
use Application\Entities\PostEntity;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class StatsModel
{
    public array $averageContentLengthPerMonth;
    public array $longestPostsPerMonth;
    public int $totalPostsSplitPerWeek;
    public int $averagePostsPerUserPerMonth;

    protected array $analyzableData;

    protected const STATS_KEY = 'statistics_key';

    public function __construct()
    {
        $stats = $this->getFromCache();


    }

    /**
     * @return StatsModel
     * @throws GuzzleException
     */
    public function getStats(): StatsModel
    {
        $statsData = $this->getFromCache();

        if (empty($statsData) || !$statsData)
            $this->update();

        return $this;
    }

    /**
     * @return StatsModel
     * @throws GuzzleException
     */
    public function update(): StatsModel
    {
        $stats = $this->putIntoCache($this->analyze($this->getFromSource()));

        $this->analyze($stats);

        return $this;
    }

    /**
     * @return array|bool
     */
    protected function getFromCache()
    {
        return ApplicationContainer::getInstance()->cacheDriver->get(self::STATS_KEY);
    }

    /**
     * @return array|bool
     * @throws GuzzleException
     */
    protected function getFromSource(): array
    {
        $client = new Client();

        $params = 'sl_token=smslt_b0a5251831_0bfa705e06&page=1';

        $uri = 'https://api.supermetrics.com/assignment/posts' . '?' . $params;
        /*$headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];*/

        //TODO change sl token


        $result = $client->request('GET', $uri);

        return json_decode($result->getBody()->getContents(), true);
    }

    /**
     * @param array $rawData
     * @return array
     */
    protected function putIntoCache(array $rawData): array
    {
        $processedData = $rawData['data'];

        ApplicationContainer::getInstance()->cacheDriver->set(self::STATS_KEY, $processedData);

        return $processedData;
    }

    protected function analyze(array $rawData){
        $data = $rawData['data'];

        $posts = [];

        foreach ($data as $postData){
            $posts[] = new PostModel($postData);
        }

        $this->analyzableData = $posts;

        $this->defineAverageContentLengthPerMonth($data);

        return $this;
    }

    private function defineAverageContentLengthPerMonth(array $data){


        foreach ($data as $postData){
            $posts[] = new PostModel($postData);
        }
    }
}