<?php

header('Access-Control-Allow-Origin: *');
use Symfony\Component\HttpFoundation\Request;
use App\Controller\CreateCubeController;
use App\Adapter\Builder\OwnCubeBuilder;
use Domain\Shape\UseCase\CreateCube;
require_once __DIR__ . '/vendor/autoload.php';

$request = Request::createFromGlobals();

$dimension = json_decode($request->request->get('dimension')) ?? [];
$segment = array_map('intval', str_split($request->request->get('segment'))) ?? [];

$ownBuilder = new OwnCubeBuilder($dimension,$segment);

$useCase = new CreateCube($ownBuilder);

$controller = new CreateCubeController($useCase);

$response = $controller->handleRequest($request);

$response->send();
