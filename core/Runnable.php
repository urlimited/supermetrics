<?php

interface Runnable {
    function init(): self;

    function run(): self;
}