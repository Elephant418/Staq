<?php

require_once(__DIR__ . '/../../../../../vendor/autoload.php');

( new \Staq\Server )
    ->addApplication('Example\\Staq\\Bootstrap', '/Example/Staq/Bootstrap/public')
    ->addPlatform( 'local')
    ->getApp( )
    ->run( );