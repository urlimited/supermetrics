<?php

namespace Application\Entities;

class PostEntity
{
    protected string $id;

    protected string $fromName;

    protected string $fromId;

    protected string $message;

    protected string $type;

    protected string $createdTime;

    public function __construct($data){
        $this->id = $data['id'];
        $this->fromName = $data['from_name'];
        $this->fromId = $data['from_id'];
        $this->message = $data['message'];
        $this->type = $data['type'];
        $this->createdTime = $data['created_time'];
    }

    public function getId(){
        return $this->id;
    }

    public function getFromName(){
        return $this->fromName;
    }

    public function getFromId(){
        return $this->fromId;
    }

    public function getMessage(){
        return $this->message;
    }

    public function getType(){
        return $this->type;
    }

    public function getCreatedTime(){
        return $this->createdTime;
    }
}