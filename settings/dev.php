<?php
$config['path']['uploads']['storage'] = '/storage';
$config['path']['uploads']['url'] = '//localhost:8000/storage';

$config['sys']['elastic']['hosts'] = ["elastic:9200"];

$config['sys']['celery']['host'] = 'redis';
$config['sys']['celery']['port'] = 6379;
$config['sys']['celery']['db'] = 1;

$config['sys']['cache']['servers'] = [
    [
        'host' => 'redis',
        'port' => 6379,
        'dbindex' => 4,
    ],
];

$config['sys']['logs']['dir'] = '/log';

$config['misc']['debug'] = true;

$config['misc']['services']['twicher'] = 'http://127.0.0.1:5000/quotes/twitchy';
$config['misc']['services']['donations'] = 'https://everypony.ru/donate_api/';
$config['misc']['services']['banners'] = 'https://projects.everypony.ru/banners/';

return $config;
