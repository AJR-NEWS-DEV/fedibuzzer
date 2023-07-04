<?php 
require 'lib/altorouter/AltoRouter.php';
require 'lib/jshrink/src/JShrink/Minifier.php';

$router = new AltoRouter();

$router->map('GET', '/', function() {
    header("Location: https://github.com/natureofmad/fedishare");
});

/*
$router->map('GET', '/api/v1/script', function() {
    header("Content-type: text/javascript");
    echo JShrink\Minifier::minify(file_get_contents("script/script.js"));
});
*/

$router->map('GET', '/api/v1/instance', function() {
    require 'api/getInstanceInfo.php';
});

// match current request url
$match = $router->match();

// call closure or throw 404 status
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}