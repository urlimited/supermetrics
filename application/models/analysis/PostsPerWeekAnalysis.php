<?php

namespace Application\Models\Analysis;

class PostsPerWeekAnalysis extends Analysis {

    /**
     * @param array $data
     * @return array
     */
    public function performAnalysis(array $data){
        return array_map(function(array $weeklyPosts) {

            return count($weeklyPosts);
        }, $this->splitPostsByWeeks($data));
    }
}