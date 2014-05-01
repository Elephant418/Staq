<?php

require_once(__DIR__ . '/../../../../../vendor/autoload.php');

( new \Staq\Server )
    ->addApplication('Example\\Staq\\Vanilla')
    ->addPlatform( 'local')
    ->getApp( )
    ->run( );