<?php

namespace Core;

class Controller {
    public function beforeCall(){

    }

    public function afterCall($result): void{
        echo $result;
    }
}