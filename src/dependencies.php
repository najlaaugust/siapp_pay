<?php


$container = $app->getContainer();

// dbguy
$container['dbguy'] = function ($c) {
    $settings = $c->get('settings')['dbguy'];
    return new dbguy($settings['serverName'],$settings['uid'],$settings['pwd'],$settings['database']);    
};

//monolog
$container['logger'] = function($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new \Monolog\Logger($settings['name']);
    $file_handler = new \Monolog\Handler\StreamHandler($settings['path']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler($file_handler, Monolog\Logger::DEBUG);
    return $logger;
};

