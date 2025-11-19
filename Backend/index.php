<?php

require_once __DIR__ . '/services/OpenAiService.php';
require_once __DIR__ . '/services/ResponseService.php';
require_once __DIR__ . '/routes/web.php';
require_once __DIR__ . '/config/connection.php';

// to get the route from axios
$request = $_GET['route'] ?? '/';

//to get token from header
$token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;

//read json body and transform to associative array
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?? [];

//calling specific controller and method depending on the route
if (isset($apis[$request])) {
    $controller_name = $apis[$request]['controller'];
    $method          = $apis[$request]['method'];

    require_once __DIR__ . "/controllers/{$controller_name}.php";
    $controller = new $controller_name();

    if (!method_exists($controller, $method)) {
        echo ResponseService::response(
            500,
            "Error: Method {$method} not found in {$controller_name}"
        );
        exit;
    }
    if($method === "createEntry"){
        $resp = OpenAIService::parseEntryText($data['raw_text']);
        $resp['raw_text'] = $data['raw_text'];
        $response = $controller->$method($connection,$token,$resp);
        echo $response;
        exit;
    }
    //sending data to method
    $response = $controller->$method($connection, $token, $data);

    echo $response;
} else {
    echo ResponseService::response(404, "Route Not Found: " . $request);
}
