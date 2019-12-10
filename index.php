<?php

error_reporting(E_ALL);
require 'inc.php';

$serviceController = new app\controllers\ServiceController;

$requestMethod = $_SERVER['REQUEST_METHOD'];
$isGet = 'GET' == $requestMethod;
$isPut = 'PUT' == $requestMethod;

$k1 = $_GET['k1'] ?? null;
$v1 = $_GET['v1'] ?? null;

$k2 = $_GET['k2'] ?? null;
$v2 = $_GET['v2'] ?? null;

$k3 = $_GET['k3'] ?? null;

if (
    $isGet

    && 'users' == $k1
    && is_numeric($v1)

    && 'services' == $k2
    && is_numeric($v2)

    && 'tarifs' == $k3
) {
    $serviceController->tariffs($v1, $v2);
} elseif (
    $isPut

    && 'users' == $k1
    && is_numeric($v1)

    && 'services' == $k2
    && is_numeric($v2)

    && 'tarif' == $k3
) {
    $serviceController->tariff($v1, $v2);
} else {
    http_response_code(404);
}
