<?php

// namespace api;

require 'autoload.php';

use Modules\Util;
use Modules\Response;

header("Content-Type: application/json");
date_default_timezone_set('America/Sao_Paulo');

$routes = array(
	'usuarios' => array(
		'get' => [
			'controller' 	=> Controllers\UsuariosController::class, 
			'callback' 		=> 'get', 
			'method' 		=> 'GET'
		],
		'create' => [
			'controller' 	=> Controllers\UsuariosController::class, 
			'callback' 		=> 'create', 
			'method' 		=> 'POST'
		]
	),
	'auth' => array(
		'login' => [
			'controller' 	=> Controllers\AuthController::class, 
			'callback' 		=> 'login', 
			'method' 		=> 'POST'
		],
		'logout' => [
			'controller' 	=> Controllers\AuthController::class, 
			'callback' 		=> 'logout', 
			'method' 		=> 'POST'
		],
		'logout/all' => [
			'controller' 	=> Controllers\AuthController::class, 
			'callback' 		=> 'logoutAll', 
			'method' 		=> 'POST'
		],
		'validate' => [
			'controller' 	=> Controllers\AuthController::class, 
			'callback' 		=> 'validate', 
			'method' 		=> 'POST'
		]
	),
	'produtos' => array(
		'get' => [
			'controller' 	=> Controllers\ProdutosController::class, 
			'callback' 		=> 'get', 
			'method' 		=> 'GET'
		],
		'import' => [
			'controller' 	=> Controllers\ProdutosController::class, 
			'callback' 		=> 'import', 
			'method' 		=> 'POST'
		],
		'delete' => [
			'controller' 	=> Controllers\ProdutosController::class, 
			'callback' 		=> 'delete', 
			'method' 		=> 'DELETE'
		]
	),
);

$uri = Util::getURI();
if (!array_key_exists($uri['prefix'], $routes)) response(new Response([
	'error' => 'Route not found!!'
], 404));

if (!array_key_exists($uri['sulfix'], $routes[$uri['prefix']])) response(new Response([
	'error' => 'Route not found!!'
], 404));

if ($routes[$uri['prefix']][$uri['sulfix']]['method'] !== $_SERVER['REQUEST_METHOD']) response(new Response([
	'error' 	=> 'Method '.$_SERVER['REQUEST_METHOD'].' is not allowed to this route!',
	'allowed' 	=> $routes[$uri['prefix']][$uri['sulfix']]['method']
], 404));

$class 	= $routes[$uri['prefix']][$uri['sulfix']]['controller'];
$callback = $routes[$uri['prefix']][$uri['sulfix']]['callback'];

$controller = new $class();
try {
	response($controller->call($callback));
} catch (\Error | \Exception $e) {
	response(new Response([
		'error' 	=> $e->getMessage(), 
		'file' 		=> $e->getFile(),
		'line' 		=> $e->getLine(),
		'traceback' => $e->getTrace(),
		'exception' => $e->__toString(),
	], 500));
}

function response(Response $response) {
	http_response_code($response->status);
	echo $response->json;
	exit();
}

?>