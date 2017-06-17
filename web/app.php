<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';

$env = getenv('ENV') ?: 'prod';
$debug = boolval(getenv('DEBUG'));
$kernel = new \NiR\GhDashboard\Symfony\AppKernel($env, $debug);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
