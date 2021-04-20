<?php

namespace Application\Models\Analysis;

use Application\Models\PostModel;

class LongestPostPerMonthsAnalysis extends Analysis {

    /**
     * @param array $data
     * @return array
     */
    public function performAnalysis(array $data){
        return array_map(function(array $monthlyPosts) {
            $monthlyPostsLengths = array_map(function(PostModel $post){
                return $post->getContentLength();
            }, $monthlyPosts);

            if(count($monthlyPostsLengths) === 0)
                return 0;

            return max($monthlyPostsLengths);
        }, $this->splitPostsByMonths($data));
    }
}