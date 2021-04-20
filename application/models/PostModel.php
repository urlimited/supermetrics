<?php

namespace Application\Models;

use Application\Entities\PostEntity;

class PostModel {
    protected PostEntity $entity;

    public int $date;

    public int $contentLength;

    public function __construct(array $dataForEntity){
        $this->entity = new PostEntity($dataForEntity);

        $this->date = $dataForEntity['created_time'];
    }

}