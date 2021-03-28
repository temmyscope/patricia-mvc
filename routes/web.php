<?php
use App\Auth;

//$router->addRoute(['GET', 'OPTIONS'], '/', [ ContentController::class, "all" ]);

$router->get('/', function($request, $response){
	return $response->send(
		"Welcome to Patricia-MVC API: Endpoints are /login, /register & /logout", 
	200);
});

$router->post('login', [ AuthController::class, 'login' ]);

$router->post('register', [ AuthController::class, 'register' ]);

$router->use('api-auth;', function () use ($router) {

	$router->post('logout', function($request, $response){
		Auth::logout($request->user);
		return $response->send([
			'message' => "Session has ended.",
			'success' => 'true'
		], 200);
	});

});