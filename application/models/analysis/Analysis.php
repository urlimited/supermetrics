<?php

namespace Application\Models\Analysis;

abstract class Analysis {
    /**
     * @param $posts
     * @return mixed|null
     */
    protected function splitPostsByMonths($posts){
        return array_reduce($posts, function($accum, $next){
            $monthNumber = $next->getCreatedMonth();

            $accum[$monthNumber][] = $next;

            return $accum;
        }, [
            'Jan' => [], 'Feb' => [], 'Mar' => [], 'Apr' => [], 'May' => [], 'Jun' => [], 'Jul' => [], 'Aug' => [], 'Sep' => [], 'Oct' => [], 'Nov' => [], 'Dec' => [],
        ]);
    }

    /**
     * @param $posts
     * @return array
     */
    protected function splitPostsByWeeks($posts){
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

    /**
     * @param $posts
     * @return array
     */
    protected function splitPostsByUsers($posts){
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