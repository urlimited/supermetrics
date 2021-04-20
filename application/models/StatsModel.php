<?php

namespace Application\Models;

use Application\ApplicationContainer;
use Application\Models\Analysis\AverageContentLengthPerMonthAnalysis;
use Application\Models\Analysis\LongestPostPerMonthsAnalysis;
use Application\Models\Analysis\PostsPerUsersPerMonthsAnalysis;
use Application\Models\Analysis\PostsPerWeekAnalysis;
use Application\SuperMetricsApiHandler;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

class StatsModel
{
    public array $averageContentLengthPerMonth;
    public array $longestPostsPerMonth;
    public array $postsCountPerWeek;
    public array $averagePostsPerUserPerMonth;

    protected const STATS_KEY = 'statistics_key';

    /**
     * @return array
     * @throws GuzzleException
     */
    public function getStats(): array
    {
        $statsData = $this->getFromCache();

        if ($statsData !== false && !empty($statsData))
            return $statsData;

        $this->update();

        return [
            'average_characted_length_of_posts_per_month' => $this->averageContentLengthPerMonth,
            'longest_post_per_month' => $this->longestPostsPerMonth,
            'count_posts_per_week' => $this->postsCountPerWeek,
            'average_posts_per_user_per_month' => $this->averagePostsPerUserPerMonth,
        ];
    }

    /**
     * @return StatsModel
     * @throws GuzzleException
     * @throws Exception
     */
    public function update(): StatsModel
    {
        $data = $this->getFromSource();

        $this->analyze($data);

        $this->putIntoCache([
            'average_characted_length_of_posts_per_month' => $this->averageContentLengthPerMonth,
            'longest_post_per_month' => $this->longestPostsPerMonth,
            'count_posts_per_week' => $this->postsCountPerWeek,
            'average_posts_per_user_per_month' => $this->averagePostsPerUserPerMonth,
        ]);

        return $this;
    }

    /**
     * @return array|bool
     */
    protected function getFromCache()
    {
        $fromCache = ApplicationContainer::getInstance()->cacheDriver->get(self::STATS_KEY);

        if($fromCache['put_at'] < (time() - 60 * 60 * 24))
            return false;

        return $fromCache['data'];
    }

    /**
     * @return array|bool
     * @throws GuzzleException
     */
    protected function getFromSource(): array
    {
        $postsData = [];

        $downloadedPostsCount = 0;
        $page = 1;

        while ($downloadedPostsCount < 1000) {
            /**
             * @var $api SuperMetricsApiHandler
             */
            $api = SuperMetricsApiHandler::getInstance();

            $postsReceived = $api->getPosts($page);

            $page++;

            $postsData = array_merge($postsData, $postsReceived);

            $downloadedPostsCount += count($postsReceived);
        }

        return $postsData;
    }

    /**
     * @param array $rawData
     * @return array
     */
    protected function putIntoCache(array $rawData): array
    {
        $processedData = $rawData;

        ApplicationContainer::getInstance()->cacheDriver->set(self::STATS_KEY, [
            'put_at' => time(),
            'data' => $processedData
        ]);

        return $processedData;
    }

    /**
     * @param array $rawData
     * @throws Exception
     */
    protected function analyze(array $rawData)
    {
        $data = $rawData;

        $posts = [];

        foreach ($data as $postData) {
            $posts[] = new PostModel($postData);
        }

        $this->averageContentLengthPerMonth = (new AverageContentLengthPerMonthAnalysis())->performAnalysis($posts);
        $this->longestPostsPerMonth = (new LongestPostPerMonthsAnalysis())->performAnalysis($posts);
        $this->postsCountPerWeek = (new PostsPerWeekAnalysis())->performAnalysis($posts);
        $this->averagePostsPerUserPerMonth = (new PostsPerUsersPerMonthsAnalysis())->performAnalysis($posts);
    }
}