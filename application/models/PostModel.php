<?php

namespace Application\Models;

use Application\Entities\PostEntity;
use DateTime;
use Exception;

class PostModel {
    protected PostEntity $entity;

    protected int $date;

    protected int $contentLength;

    /**
     * PostModel constructor.
     * @param array $dataForEntity
     * @throws Exception
     */
    public function __construct(array $dataForEntity){
        $this->entity = new PostEntity($dataForEntity);

        $this->date = (new DateTime($dataForEntity['created_time']))->getTimestamp();

        $this->contentLength = strlen($this->entity->getMessage());
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCreatedMonth(){
        return (new DateTime())->setTimestamp($this->date)->format('M');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCreatedWeek(){
        return (new DateTime())->setTimestamp($this->date)->format('W');
    }

    public function getContentLength(){
        return $this->contentLength;
    }

    public function getUserId(){
        return $this->entity->getFromId();
    }

}