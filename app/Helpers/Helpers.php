<?php
/*
|-----------------------------------------------------------------------------|
|           Framework Dependencies: Don't Remove Any Function                 |
|-----------------------------------------------------------------------------|
*/
if (!function_exists('redirect')) {
    function redirect($base_url, $location = '')
    {
        $location = $base_url . "/{$location}";
        if (!headers_sent()) {
            header("location: $location");
            exit();
        } else {
            echo "<script type='text/javascript'> window.location.href= '{$location}';</script>";
            echo '<noscript> <meta http-equiv="refresh" content="0;url=' . $location . '"/></noscript>';
            exit();
        }
    }
}

if (!function_exists('env')) {
    function env($key, $fallback = ''){
        return $_ENV[$key] ?? $_SERVER[$key] ?? $fallback;
    }
}

function app(){
    global $app;
    return $app;
}

function config(){
    global $app;
    return $app->config();
}

function app_url(){
    return app()->config()->get('APP_URL');
}

function dnd($var)
{
    echo "<pre style='background-color:black;color:green;font-size:15px;'>";
    var_dump($var);
    echo "<pre>";
    die();
}
