<?php

require_once(__DIR__ . '/../../../../../vendor/autoload.php');

( new \Staq\Server )
    ->addApplication('Example\\Staq\\Bootstrap')
    ->addPlatform( 'local')
    ->getApp( )
    ->run( );