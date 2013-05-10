<?php

require_once(__DIR__ . '/../../../../../vendor/autoload.php');

\Staq\App::create('Example\\Staq\\Bootstrap')
    ->setPlatform('local')
    ->run();

?>