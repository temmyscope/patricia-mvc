<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

use Seven\Router\Router;
use App\Auth;


/*
|---------------------------------------------------------------------------|
| Register The Auto Loader 																									|
|---------------------------------------------------------------------------|
|
*/

require __DIR__.'/vendor/autoload.php';

$app = new App\Providers\Application(__DIR__);
$request = $app->request(); $response = $app->response();

/*
|
|------------------------------------------------------------------------------|
| Load Environment Variables                                                   |
|------------------------------------------------------------------------------|
|
*/

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_DRIVER']);

/*
|
|------------------------------------------------------------------------------|
| Initialize Router And Start Routing Process                                  |
|------------------------------------------------------------------------------|
|
*/

$router = new Router(
    'App\Http\Controllers', $request, $response
);

//$router->enableCache(__DIR__.'/cache');

$router->registerProviders($request, $response);


$router->middleware('api-auth', function ($request, $response, $next){
    $token = $request->bearerToken();
    if (!$token || !Auth::isValid($token)) {
        return $response->send("Unauthorized.", 407);
    }
    $user = Auth::getValuesFromToken($token);
    $request->user = $user->user; 
    $request->email = $user->email;
    $next($request, $response);
});


require __DIR__.'/routes/web.php';


$router->run($_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO'] ?? '/');