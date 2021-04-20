<?php

namespace Application\Models;

use Application\ApplicationContainer;
use Application\Entities\PostEntity;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class StatsModel
{
    public array $averageContentLengthPerMonth;
    public array $longestPostsPerMonth;
    public array $postsCountPerWeek;
    public array $averagePostsPerUserPerMonth;

    protected array $analyzableData = [];

    protected const STATS_KEY = 'statistics_key';

    public function __construct()
    {
        $stats = $this->getFromCache();


    }

    /**
     * @return array
     * @throws GuzzleException
     */
    public function getStats(): array
    {
        /*$statsData = $this->getFromCache();

        if (empty($statsData) || !$statsData)
            $this->update();*/

        //$this->analyze($this->getFromCache());

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
        return ApplicationContainer::getInstance()->cacheDriver->get(self::STATS_KEY);
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

        $client = new Client();

        while($downloadedPostsCount < 1000){
            $params = 'sl_token=smslt_edba2bacf9660_6deec1aca39f9d5&page=' . $page++;

            $uri = 'https://api.supermetrics.com/assignment/posts' . '?' . $params;

            $result = $client->request('GET', $uri);

            $posts = json_decode($result->getBody()->getContents(), true)['data']['posts'];

            $postsData = array_merge($postsData, $posts);

            $downloadedPostsCount += count($posts);
        }

        /*$headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];*/

        //TODO change sl token


        return $postsData;
    }

    /**
     * @param array $rawData
     * @return array
     */
    protected function putIntoCache(array $rawData): array
    {
        $processedData = $rawData;

        ApplicationContainer::getInstance()->cacheDriver->set(self::STATS_KEY, $processedData);

        return $processedData;
    }

    /**
     * @param array $rawData
     * @throws Exception
     */
    protected function analyze(array $rawData){
        $data = $rawData;

        $posts = [];

        foreach ($data as $postData){
            $posts[] = new PostModel($postData);
        }

        $this->analyzableData = $posts;

        $this->averageContentLengthPerMonth = $this->defineAverageContentLengthPerMonth();
        $this->longestPostsPerMonth = $this->defineLongestPostPerMonth();
        $this->postsCountPerWeek = $this->definePostsSplitPerWeek();
        $this->averagePostsPerUserPerMonth = $this->defineAveragePostsPerUserPerMonth();
    }

    /**
     * @param array $data
     * @return int[]
     * @throws Exception
     */
    private function defineAverageContentLengthPerMonth(){
        if(empty($this->analyzableData))
            throw new Exception('Analyzable data array should not be empty');

        return array_map(function(array $monthlyPosts) {
            $monthlyPostsLengths = array_map(function(PostModel $post){
                return $post->getContentLength();
            }, $monthlyPosts);

            if(count($monthlyPostsLengths) === 0)
                return 0;

            return (int) round(array_sum($monthlyPostsLengths) / count($monthlyPostsLengths));
        }, $this->splitPostsByMonths($this->analyzableData));
    }

    /**
     * @return array
     * @throws Exception
     */
    private function defineLongestPostPerMonth(){
        if(empty($this->analyzableData))
            throw new Exception('Analyzable data array should not be empty');

        return array_map(function(array $monthlyPosts) {
            $monthlyPostsLengths = array_map(function(PostModel $post){
                return $post->getContentLength();
            }, $monthlyPosts);

            if(count($monthlyPostsLengths) === 0)
                return 0;

            return max($monthlyPostsLengths);
        }, $this->splitPostsByMonths($this->analyzableData));
    }

    /**
     * @throws Exception
     */
    private function definePostsSplitPerWeek(){
        if(empty($this->analyzableData))
            throw new Exception('Analyzable data array should not be empty');

        return array_map(function(array $weeklyPosts) {

            return count($weeklyPosts);
        }, $this->splitPostsByWeeks($this->analyzableData));
    }

    /**
     * @return array
     * @throws Exception
     */
    private function defineAveragePostsPerUserPerMonth(){
        if(empty($this->analyzableData))
            throw new Exception('Analyzable data array should not be empty');

        return array_map(function(array $perUserPosts) {
            return array_map(function(array $monthlyPosts) {
                $monthlyPostsLengths = array_map(function(PostModel $post){
                    return $post->getContentLength();
                }, $monthlyPosts);

                if(count($monthlyPostsLengths) === 0)
                    return 0;

                return max($monthlyPostsLengths);
            }, $this->splitPostsByMonths($perUserPosts));


        }, $this->splitPostsByUsers($this->analyzableData));
    }

    private function splitPostsByMonths($posts){
        return array_reduce($posts, function($accum, $next){
            $monthNumber = $next->getCreatedMonth();

            $accum[$monthNumber][] = $next;

            return $accum;
        }, [
            'Jan' => [], 'Feb' => [], 'Mar' => [], 'Apr' => [], 'May' => [], 'Jun' => [], 'Jul' => [], 'Aug' => [], 'Sep' => [], 'Oct' => [], 'Nov' => [], 'Dec' => [],
        ]);
    }

    private function splitPostsByWeeks($posts){
        $weeks = [];

        // In a year we might have 54 weeks, if the first day of the year is sunday/saturday (depends on first day in calendar)
        for($i = 1; $i < 54; $i++){
            $processedIncrementer = $i;

            if($i < 10)
                $processedIncrementer = '0' . $i;

            $weeks[$processedIncrementer] = [];
        }

        return array_reduce($posts, function($accum, $next){
            $weekNumber = $next->getCreatedWeek();

            $accum[$weekNumber][] = $next;

            return $accum;
        }, $weeks);
    }

    private function splitPostsByUsers($posts){
        $usersIds = array_unique(array_map(function($post){
            return $post->getUserId();
        }, $posts));

        $usersArray = [];

        foreach($usersIds as $key => $user){
            $usersArray[$user] = [];
        }

        return array_reduce($posts, function($accum, $next){
            $userId = $next->getUserId();

            $accum[$userId][] = $next;

            return $accum;
        }, $usersArray);
    }
}