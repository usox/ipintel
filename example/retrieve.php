<?php

use Curl\Curl;
use Usox\IpIntel\IpIntel;

require_once __DIR__ . '/../vendor/autoload.php';

$ip = '127.0.0.1';

$client = new IpIntel(
    new Curl(),
    'YOUR_EMAIL@ADDRESS.HERE'
);

var_dump($client->validate($ip));
