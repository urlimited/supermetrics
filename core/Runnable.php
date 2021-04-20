<?php

namespace Core;

interface Runnable {
    function init(): self;

    function run(): void;
}