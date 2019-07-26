<?php
require './vendor/autoload.php';

Sentry\init([
    'dsn' => 'https://9b96ae361bd64eca80897c101f2ab16b@sentry.io/1412141',
    'environment' => 'dev',
//    'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),
]);

throw new Exception("My first Sentry error!");

echo PHP_EOL;