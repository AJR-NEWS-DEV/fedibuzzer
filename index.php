<?php
use MatthiasMullie\Minify;

require 'vendor/autoload.php';

$router = new AltoRouter();

$router->setBasePath('/fedishare');

$router->map('GET', '/', function() {
    header("Location: https://github.com/natureofmad/fedishare");
});

$router->map('GET', '/demo', function() {
    require 'api/demo.php';
});

$router->map('GET', '/api/v1/script', function() {
    header("Content-type: text/javascript");
    $packer = new Minify\JS("script/script.js");
    $packed = $packer->minify();

    echo $packed;
});

$router->map('GET', '/api/v1/instance/[*:url]', function($url) {
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